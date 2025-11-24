# Fix: 100% Exact Seat Number Sync dari WooCommerce

**Tanggal**: 2025-11-24
**Status**: ✅ COMPLETED

---

## Masalah Sebelumnya

Sebelum fix ini, sistem **TIDAK** menggunakan nomor kursi dari WooCommerce. Yang terjadi:

1. Customer pilih kursi **B3, C5** di website (online)
2. WooCommerce kirim data kursi via API
3. **❌ POS LOKAL IGNORE** nomor kursi dari WooCommerce
4. POS auto-assign kursi dari depan → Customer dapat **A1, A2** (SALAH!)

**Root Cause**:
- `WooCommerceService::parseOrderToTransaction()` tidak extract `seat_name` dari `_wbtm_ticket_info`
- `SyncFromWooCommerce::createSeatBookings()` selalu auto-assign kursi, tidak pakai data dari WooCommerce

---

## Solusi

### File 1: `app/Services/WooCommerceService.php`

**Perubahan Line 195-271**:

✅ **SEBELUM**: Hanya ambil `ticketInfo[0]` (ticket pertama saja)
```php
$ticketInfo = is_array($ticketInfo) ? $ticketInfo[0] : [];
// seat_name TIDAK di-extract!
```

✅ **SESUDAH**: Loop semua tickets, extract `seat_name` dari tiap ticket
```php
$seatNumbers = [];
foreach ($ticketInfo as $index => $ticket) {
    if (isset($ticket['seat_name'])) {
        $seatNumbers[$index + 1] = $ticket['seat_name'];  // Extract seat!
    }
}

return [
    ...
    'seat_numbers' => $seatNumbers,  // 🎯 BARU!
    ...
];
```

**Benefit**:
- Extract semua seat dari WooCommerce metadata `_wbtm_ticket_info`
- Format: `{"1": "A1", "2": "B3", "3": "C5"}`
- Hitung passenger by type (adult/child/toddler) dengan benar

---

### File 2: `app/Console/Commands/SyncFromWooCommerce.php`

**Perubahan Line 270-310 (createTickets)**:

✅ **SEBELUM**: Ticket seat_number selalu `NULL`
```php
Ticket::create([
    'seat_number' => null,  // ❌ Selalu NULL
]);
```

✅ **SESUDAH**: Langsung pakai seat dari WooCommerce
```php
$seatNumbers = $parsedData['seat_numbers'] ?? [];
$seatNumber = $seatNumbers[$ticketIndex] ?? null;

Ticket::create([
    'seat_number' => $seatNumber,  // ✅ Dari WooCommerce!
]);
```

---

**Perubahan Line 343-463 (createSeatBookings)**:

✅ **SEBELUM**: Selalu auto-assign dari depan
```php
foreach ($adultPassengers as $index => $passengerName) {
    $assignedSeat = $availableSeats[$index];  // ❌ Auto-assign A1, A2, A3...
}
```

✅ **SESUDAH**: 2 mode dengan prioritas WooCommerce seat
```php
$seatNumbers = $parsedData['seat_numbers'] ?? [];

if (!empty($seatNumbers)) {
    // MODE 1: Use exact seats from WooCommerce (100% match)
    $this->info("🎯 Using seat numbers from WooCommerce");

    // Validate seats available
    foreach ($seatNumbers as $seatNumber) {
        if (in_array($seatNumber, $bookedSeats)) {
            throw new Exception("Seat conflict! {$seatNumber} already booked");
        }
    }

    // Assign exact seats
    $assignedSeat = $seatNumbers[$index + 1];

} else {
    // MODE 2: Fallback auto-assign (backward compatibility)
    $this->warn("⚠️ No seats from WooCommerce, using auto-assignment");
    $assignedSeat = $availableSeats[$index];
}
```

**Benefit**:
- ✅ Gunakan nomor kursi EXACT dari WooCommerce
- ✅ Validasi seat conflict (jika kursi sudah dibooking offline)
- ✅ Fallback ke auto-assign jika WooCommerce tidak kirim seat (backward compatibility)
- ✅ Clear logging untuk debugging

---

## Hasil Akhir

### ✅ SEKARANG (After Fix):

```
ONLINE (WooCommerce):
- Customer pilih kursi: B3, C5
- WooCommerce kirim: {"1": "B3", "2": "C5"}

SYNC KE POS LOKAL:
- System extract seat_numbers: ["B3", "C5"]
- System validasi: B3 available? C5 available? ✅
- System create SeatBooking:
  → Ticket #1: B3 ✅
  → Ticket #2: C5 ✅

RESULT: 🎯 100% EXACT MATCH!
```

### Conflict Detection:

```
SCENARIO: Double Booking
- Customer booking online: Seat A1
- Kasir booking offline: Seat A1 (secara bersamaan)

SYNC PROCESS:
1. WooCommerce kirim order dengan seat A1
2. System cek: A1 already booked locally? YES
3. ❌ THROW EXCEPTION: "Seat conflict! Seat A1 already booked"
4. Order masuk sync_queue dengan status 'failed'
5. Admin harus resolve manual (hubungi customer, ganti seat)
```

---

## Testing

### Test 1: Manual Sync
```bash
php artisan woocommerce:sync-from --limit=5
```

**Expected Output**:
```
🎯 Using seat numbers from WooCommerce: A1, B2, C3
✅ Assigned seats to 3 adults: A1, B2, C3
✅ Successfully synced order #6300 → Transaction #WC-6300-ABC123
```

### Test 2: Check Database
```sql
-- Cek seat numbers dari WooCommerce order
SELECT
    t.id,
    t.transaction_code,
    t.woocommerce_order_id,
    tk.seat_number,
    tk.passenger_name
FROM transactions t
JOIN tickets tk ON tk.transaction_id = t.id
WHERE t.woocommerce_order_id IS NOT NULL
ORDER BY t.id DESC
LIMIT 10;
```

**Expected**: Seat numbers harus match dengan yang dipilih customer di website!

---

## Backward Compatibility

✅ **Tetap support order lama** yang tidak punya `seat_name`:
- Jika `seat_numbers` kosong → fallback ke auto-assign
- Warning message: "⚠️ No seat numbers from WooCommerce, using auto-assignment"
- Old behavior tetap jalan untuk data legacy

---

## API Data Structure

### WooCommerce → Laravel

**Input dari WooCommerce API** (`line_items[0].meta_data`):
```json
{
  "_wbtm_ticket_info": [
    {
      "seat_name": "A1",
      "ticket_type": "0",
      "ticket_qty": "1",
      "ticket_price": "150000"
    },
    {
      "seat_name": "B3",
      "ticket_type": "0",
      "ticket_qty": "1",
      "ticket_price": "150000"
    }
  ],
  "seat_passenger_map": {
    "1": "John Doe",
    "2": "Jane Doe"
  }
}
```

**Output dari parseOrderToTransaction()**:
```php
[
    'woocommerce_order_id' => 6300,
    'adult_count' => 2,
    'seat_numbers' => [
        1 => 'A1',
        2 => 'B3'
    ],
    'seat_passenger_map' => [
        1 => 'John Doe',
        2 => 'Jane Doe'
    ],
    ...
]
```

---

## Monitoring

### Logs to Check:
```bash
# Success logs
tail -f storage/logs/laravel.log | grep "Using seat numbers from WooCommerce"

# Error logs (conflicts)
tail -f storage/logs/laravel.log | grep "Seat conflict"
```

### Database Query:
```sql
-- Check sync errors
SELECT
    id,
    transaction_code,
    woocommerce_order_id,
    sync_error
FROM transactions
WHERE sync_error LIKE '%Seat conflict%'
ORDER BY created_at DESC;
```

---

## Troubleshooting

### Problem: "Seat conflict! Seat A1 already booked"

**Penyebab**: Kursi A1 sudah dibooking offline sebelum sync online selesai

**Solusi**:
1. Check siapa yang booking offline:
   ```sql
   SELECT * FROM seat_bookings
   WHERE seat_number = 'A1'
   AND schedule_id = X
   AND departure_date = 'Y';
   ```
2. Hubungi customer online, minta ganti seat
3. Atau cancel booking offline (jika masih bisa)
4. Retry sync: `php artisan woocommerce:sync-from`

---

### Problem: "No seat numbers from WooCommerce, using auto-assignment"

**Penyebab**: WooCommerce plugin tidak kirim `seat_name` di `_wbtm_ticket_info`

**Solusi**:
1. Check WooCommerce plugin version (harus support seat assignment)
2. Check product settings di WooCommerce (enable seat selection)
3. Test manual order di website, pastikan customer bisa pilih kursi

---

## Files Changed

1. ✅ `app/Services/WooCommerceService.php` (Line 195-271)
2. ✅ `app/Console/Commands/SyncFromWooCommerce.php` (Line 270-463)

**Total Lines Modified**: ~200 lines

---

## Next Steps (Optional Improvements)

1. **Real-time Sync**: Gunakan WebSocket untuk instant seat update (prevent conflicts)
2. **Seat Reservation**: Lock seat selama 10 menit saat customer checkout online
3. **Auto-conflict Resolution**: Jika conflict, auto-reassign ke kursi terdekat
4. **Customer Notification**: Email customer jika seat berubah karena conflict

---

## Kesimpulan

✅ **FIX COMPLETED**
✅ Seat numbers sekarang **100% sama** dengan yang dipilih customer online
✅ Conflict detection mencegah double booking
✅ Backward compatible dengan order lama
✅ Clear logging untuk monitoring

**Testing**: Siap untuk production test dengan real WooCommerce orders!
