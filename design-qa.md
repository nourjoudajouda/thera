# THERA — Monthly appointments design QA

Reviewed: 2026-10-15. This report supersedes the initial frontend-installation report; its scope is the monthly appointments view, not every dashboard page.

## Reference and implementation

- Reference: `C:/Users/Nour/AppData/Local/Temp/codex-clipboard-e0245f91-286b-489d-8511-7d97fde22073.png` and the original `Doctor Appointments.html` monthly section.
- Desktop evidence: `C:/Users/Nour/AppData/Local/Temp/thera-month-calendar-desktop.png`.
- Mobile evidence: `C:/Users/Nour/AppData/Local/Temp/thera-month-calendar-mobile.png`.
- Desktop viewport: 1366 × 651, corresponding to the reference's page area excluding browser chrome/taskbar. Both images were inspected together. Existing shared shell offsets are retained.
- Browser verification used the isolated local SQLite QA database. Real database records were not changed. Dates, doctor identity, numbers and availability intentionally come from actual records, rather than the reference's October 2024 mock numbers.

## Comparison and corrections

- Restored the inline date navigation, current-period control, daily/weekly/monthly switch, and collapsed filter button.
- Restored three sky/amber/violet KPI panels, seven RTL weekday columns, 105px day cells, outside-month placeholders and availability colors.
- Reused Tajawal/Inter typography, Material Symbols and the existing THERA logo. No replacement illustrations or generated assets.
- Adjusted heading spacing and period-navigation width after comparing desktop screenshots.
- Fixed a pre-existing Tailwind `.collapse` visibility collision that hid the new Bootstrap filter contents.
- Checked labels, focus outlines, linked day cells, semantic controls and actual special-hours/holiday labels. Zero capacity or an inactive schedule is not presented as an available day.
- At 390px, controls wrap and KPI cards stack. Only the calendar scrolls horizontally (325px container / 680px grid); the document does not overflow horizontally. At 820px the document also remains within the viewport. Weekday labels retain one-line rendering on mobile.
- Tested next/current month, category filtering, opening a day, and switching daily/weekly/monthly through the existing AJAX navigation. Seven weekly cards and the daily appointment table remain functional.

## Functional checks

- Appointment feature tests cover calendar weekday alignment, doctor isolation, counts beyond the old 200-row pagination limit, cancelled bookings, capacity states, special hours, leave, empty category selection and the daily alias.
- The full PHP suite and the existing JavaScript AJAX regression test passed; final counts are recorded in the task result.
- Pint passed for changed PHP files. `--dirty` is unavailable because this workspace has no Git repository.
- No browser errors observed. The existing Tailwind CDN production-warning remains outside this change's scope; Bootstrap is used for the new responsive layout.

No unresolved P0/P1/P2 findings in this monthly-view scope. This is design-intent fidelity with dynamic data, not a claim that differently dated calendars are pixel-identical.

## Daily / weekly restoration and calendar colors

Latest scope: original `C:/Users/Nour/Desktop/Thera/Thera_v2 Doctor Frontend/Doctor Appointments.html`, daily reference `C:/Users/Nour/AppData/Local/Temp/codex-clipboard-f22aecdb-8d44-407d-9e5e-645b08de6557.png`, weekly reference `C:/Users/Nour/AppData/Local/Temp/codex-clipboard-a6aa1a88-3b82-467e-a17f-4747082bca24.png`.

- Evidence: `C:/Users/Nour/AppData/Local/Temp/thera-appointments-day-desktop.png`, `thera-appointments-week-desktop.png`, `thera-appointments-day-mobile.png`, `thera-appointments-week-mobile.png`, and `thera-calendar-colors.png` in the same directory.
- Desktop captures are 1366 × 651 CSS pixels at browser density 1. Reference screenshots are 1366 × 768, with 87px browser chrome and 30px taskbar excluded from the comparison. Weekly capture is scrolled so its filter panel begins at approximately y=78, matching the reference's page-relative y=81.
- Compared both reference/implementation pairs together at full resolution. Inspected filter typography, timeline time blocks, card headings, badges, and action spacing within those paired images; separate crops were unnecessary because these details remain legible.
- Iteration 1: replaced the daily table with the source-style timeline and three colored KPI panels; replaced simplified weekly boxes with the source's four-column desktop board, rounded appointment cards, searchable filters and seven days. Found excessive timeline row height from an additional facility line; moved facility information to the service tooltip. Also discarded a desktop screenshot captured during the shared shell's responsive transition.
- Iteration 2: recaptured after the responsive transition and compared again. Restored compact 83px timeline rows, source-style time cards, alternating backgrounds, blue arrival highlights, and weekly filter/card density. Dates, names, counts and empty days intentionally reflect persisted data rather than the October 2024 mock.
- Fonts/typography: retained Tajawal, Inter numbers and the source's 10–14px detail hierarchy. Spacing/layout: retained source card radii, padding, four columns at 1366px, two at tablet widths and one on small mobile widths. Colors/tokens: retained sky/amber/emerald summary surfaces and blue actions. Assets/icons: reused the shared THERA logo and Material Symbols; no generated or replacement image assets. Copy/content: Arabic, escaped data, clear unconnected-state messages.
- Intentional functional boundary: payment, queue readiness and visits are not implemented by this styling request. Their summary figures display an em dash with an explanation; the payment filter is disabled. Actual arrival badges must not claim clinical readiness or payment. Appointment details and manual arrival are functional; fake visit/payment actions and fake available-time tiles from the mock were not copied.
- Verified browser interactions: daily/weekly/monthly switching, next week, patient search (6 to 2 cards), Select2 arrival filter (6 to 2 cards), reset, opening day links, and manual arrival confirmation. Manual arrival updated the same daily view and patient badge through AJAX with a success alert.
- Responsive evidence: mobile 390 × 844 and tablet 820 × 1180. Mobile document width 375px including the scrollbar allowance, with no horizontal overflow; weekly cards become one column. Tablet document width remains 820px. Sidebar/topbar remain shared.
- Calendar state colors: open bookings green, exhausted capacity red, non-working/unavailable yellow. Limited availability is an orange badge, special hours a purple badge, today a blue outline, and neighboring-month days gray. Status labels remain visible in addition to color.
- Tests: complete PHP suite passed (243 tests / 1176 assertions); appointment feature tests cover rendering, isolation, arrival/day filtering and unconnected states. JavaScript AJAX regression passed; appointment JavaScript syntax check passed. Pint passed for the changed PHP files. Browser error log was empty.

No unresolved P0/P1/P2 visual issues in the implemented scope. Remaining integration work is explicitly marked in the UI rather than simulated.

Final capture note: the weekly screenshot was refreshed as a stable 1366 × 651 viewport capture after adding the original file's three weekly KPI panels. The extra visible header/KPIs are present in the original HTML above the supplied screenshot's scroll position. Weekly readiness/aggregate slot figures are explicitly unconnected rather than invented. The earlier matched-scroll comparison established the unchanged filter/card geometry. A full-page automation capture showed stitching/resize artifacts and was discarded; the final capture was taken from a fresh, settled desktop tab. A temporary local QA-session expiry was resolved by signing back into the existing test account. Browser automation telemetry produced unrelated network warnings; no application error was observed.

final result: passed
