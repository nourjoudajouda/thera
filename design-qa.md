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

## Patient identity workflow removal — 2026-10-31

- Scope: removed the added identity-confirmation screen, its GET/POST routes, the unlinked-appointments tab and the appointment-detail CTA. Existing patient links, clinical documents and historical data remain unchanged.
- Source: `C:/Users/Nour/Desktop/Thera/Thera_v2 Doctor Frontend/Patients List.html`.
- Source capture: `C:/Users/Nour/AppData/Local/Temp/thera-fidelity-20261031/patients-original.png`.
- Implementation captures: `C:/Users/Nour/AppData/Local/Temp/thera-fidelity-20261031/patients-no-identity.png` and `patients-no-identity-mobile.png` in the same folder.
- Compared source and implementation together in the same image input. State: Arabic, light theme, default patient table. Source contains six mock patients; implementation contains two isolated QA patients. Different row counts, missing ages and medical alerts, and the corresponding page height/scrollbar are intentional data differences, not copied clinical facts.
- Restored source scope labels, privacy notice and access-filter options. Retained required Select2 search, automatic AJAX filtering and icon-only reset. The shared doctor logo and shell intentionally follow the user's separate branding instruction.
- Fonts/typography: Tajawal and Inter retained; source heading, compact labels and table hierarchy preserved. Colors/tokens and assets: existing sky/slate/emerald palette, official logo and Material Symbols reused. Layout: original table columns and compact filters retained; mobile uses the established stacked patient cards.
- Iteration: initial comparison found bare checkbox/count controls and a search icon on the wrong side. Added the source-style compact bordered controls and corrected the icon's physical RTL positioning; recaptured and compared again. No remaining actionable P0/P1/P2 issue within this removal scope.
- Browser checks: both scope tabs, upcoming checkbox, access selector, empty results and reset passed without page reload. Console error list empty. Settled mobile viewport: 390px, document/client width 375px with scrollbar allowance, no horizontal overflow.
- Functional checks: full PHP suite 522 tests / 3321 assertions; focused patient and booking suite 54 tests / 388 assertions; JavaScript suite 31 tests. Removed endpoints return 404 and do not change existing records. Pint completed successfully.

final result: passed

## Original visits templates restored — 2026-11-01

Scope: the doctor visits directory and clinical visit workspace. The supplied HTML is the visual authority; existing clinical writes, ownership policies, revisions, immutable closed visits, prescriptions, diagnostic orders and referrals remain in place.

### Sources, captures and normalization

- Original files: `C:/Users/Nour/Desktop/Thera/Thera_v2 Doctor Frontend/Doctor Visits.html` and `Clinical Visit.html`.
- Evidence directory: `C:/Users/Nour/AppData/Local/Temp/thera-fidelity-20261031/`.
- Source screenshots: `visits-source.png`, `clinical-source.png`, `clinical-source-full.png`.
- Implementation screenshots: `visits-implementation.png`, `clinical-implementation.png`, `clinical-implementation-full.png`, `visits-mobile.png`, `clinical-mobile.png`.
- Full-view comparisons: `visits-comparison.png` and `clinical-comparison.png`; source on the left, implementation on the right. Each pair was composed and opened together, not judged from separate screenshots.
- Focused comparison: `clinical-actions-comparison.png` compares the original four action cards and current-visit items with the implementation. The target documentation column is 614 CSS px; implementation is 613.984 CSS px at the measured desktop viewport.
- CSS viewport: 1280 × 720, devicePixelRatio 1. Browser screenshots are 1265 × 712 px on both sides; combined captures are 2530 × 712 px. No density rescaling was applied. The browser's common capture bounds and scrollbar treatment are identical on both sides. Source and final implementation were captured at scrollY 0.
- State: Arabic, light theme, editable visit, no dialog open. Source uses mock patients and four visits; isolated QA has two visits and a saved initial assessment with two vital measurements. Record counts, names, dates, phone numbers, states and content are expected data differences, not replicated mock clinical facts.

### Comparison history and fixes

1. Initial P1 mismatch: directory used large individual cards, and visit actions preceded documentation inside a collapsed block. Replaced with the source's seven-column table, four KPI cards, compact tab/search/date/sort header, patient summary, 8/4 documentation-context grid, four action cards below documentation, and in-flow save/completion footer.
2. Initial P2 spacing drift: patient summary had an additional line, documentation grid differed by 8 px, vital/complaint fields were too short, and directory filters wrapped at different positions. Integrated the existing appointment link into its context line, matched the source's grid tracks, restored 24 px textarea line-height, and matched the filter tracks and KPI heights. Recaptured and compared both full-view pairs.
3. Focused P2 drift: actions inherited documentation-header padding. Matched the action header's 12 px bottom padding and 16 px gap. Re-captured the action region and compared it again. Two referral choices and the existing no-auto-send notice remain deliberately available instead of being removed to imitate the prototype's single mock button.
4. Mobile P2 control truncation: the sort label became cramped beside the date. Gave mobile filters separate full-width rows and retained an icon-only reset. Checked 390 × 844 viewport, document/client width 375 px (scrollbar allowance), without horizontal page overflow. Verified populated and empty mobile cards and the vital-sign modal's header/footer spacing.

### Required fidelity surfaces

- Fonts/typography: Tajawal for Arabic and Inter for numbers retained, matching 24 px headings, 12 px table content, 13/14 px field text and the source hierarchy.
- Layout/rhythm: source card borders, 12 px radii, compact directory density, 24 px clinical grid gaps, patient summary and action-card structure restored. Existing extra clinical fields and revision history remain accessible without changing stored content.
- Colors/tokens: source sky, slate, violet and emerald palette and status tones reused. Actual draft/initial/pending-result states remain distinguishable.
- Assets/icons: official existing THERA logo and Material Symbols retained. Shared shell/logo intentionally follows the user's separately approved doctor branding, not the prototype's smaller legacy logo. No generated substitute assets or custom drawings were needed.
- Copy/content: source headings and primary labels restored. Mock age, sex, consent/access claims, allergies, medication lists and historical results were not presented as real data. Context cards explicitly state current data availability. The diagnosis UI preserves the existing newline-text backend contract, not a new clinical catalog.

### Functional verification

- Browser: top save button persisted complaint, assessment, diagnosis, external status selection and modal vital signs through AJAX; saved values reappeared after replacement. Close-review preview includes the external status and measurements. The same existing confirmation and server close operation are retained.
- Directory: automatic tab filtering, empty results and icon-only reset worked on mobile without full navigation. Date, search, sort and doctor-scoped counters are covered by feature tests.
- Security/behavior: escaped clinical content, tenant isolation, closed-record read-only behavior, unapproved document warnings and existing actions remain covered. No clinical data is persisted to browser localStorage.
- Console errors: none in the tested directory or clinical workspace.
- Full suite: 561 PHP tests / 3586 assertions passed; JavaScript suite: 39 tests passed. Post-polish focused re-run: 30 PHP tests / 234 assertions and 4 diagnosis-adapter tests passed. Pint and whitespace checks passed.
- Browser writes used only the pre-existing isolated SQLite QA database on port 8011; production/local-user records on port 8000 were not modified.

No remaining actionable P0/P1/P2 differences within the requested template-restoration scope. Full-page height and some context-card wrapping differ intentionally because the implementation retains real data and the existing operational safeguards.

final result: passed

## Visit document popups and system-managed status — 2026-11-02

### Source and rendered evidence

- Visual source: `C:/Users/Nour/Desktop/Thera/Thera_v2 Doctor Frontend/Prescriptions.html`, `Lab Orders.html`, `Radiology Orders.html`, and the user's inline visits-tabs screenshot. Reused the actual existing logo, Tajawal/Inter fonts and Material Symbols; no substitute artwork.
- Evidence folder: `C:/Users/Nour/AppData/Local/Temp/thera-fidelity-20261031/`.
- Original captures: `rx-source.png`, `lab-source.png`, `rad-source.png`, `rx-popup-source.png`, `lab-popup-source.png`.
- Implementation: `rx-directory-final.png`, `lab-directory-final.png`, `rad-directory-final.png`, `rx-popup-final.png`, `lab-popup-final.png`, `rx-popup-mobile-final.png`, `lab-directory-mobile-v2.png`, `visits-tabs-final.png`, `visits-tabs-mobile-final.png`, `visit-closed-appointments-final.png`.
- Full-view paired evidence: `rx-directory-comparison-final.png`, `lab-directory-comparison-final.png`, `rad-directory-comparison-final.png`, `rx-popup-comparison-final.png`, `lab-popup-comparison-final.png`. Focused header/footer control comparison: `popup-controls-comparison.png`.
- Desktop CSS viewport 1280 × 720 at DPR 1. Most captures are 1280 × 720 pixels; lab source and visits-tabs captures are 1265 × 712 due to the browser's scrollbar/capture bounds. No density rescaling was applied. These small bounds differences and the source's empty lists versus actual populated QA records are explicitly not pixel-identical content states; comparison covers the shared shell, headings, tabs, toolbar, table columns, drawer dimensions, controls and surfaces.
- Mobile CSS viewport 390 × 844 at DPR 1; full-screen popup is exactly 390 × 844. Directory viewport client width is 375 with scrollbar, document scroll width also 375: no horizontal page overflow. Desktop diagnostic tables have intentional internal horizontal scrolling to retain readable columns, matching the source's responsive table container.

### Comparison history and fixes

1. P1: prescription and diagnostic lists used standalone cards instead of the supplied table design. Restored source heading/actions, status pills, compact automatic filters, original prescription eight-column table, laboratory eleven-column table and radiology ten-column table, with mobile cards.
2. P2: initial implementation heading was about 28 px too low. Matched the source main top padding and 32 px heading line-height. Compared revised directory captures against the source again.
3. P1: opening documents navigated away from the clinical visit. Added one shared Bootstrap popup with canonical editors for prescription, lab, radiology, referral and follow-up. It reuses existing saves, authorization, validation, review confirmation and immutable approved documents.
4. P2: first popup iteration was centered and too wide compared with the source drawers. Changed to left-side 576 px diagnostic/referral drawers and 672 px prescription drawer, full width on mobile. Repeated source/implementation and focused control comparisons. Kept close control opposite heading and independently scrolling body above a padded fixed footer.
5. P2: repeated form heading/save panels consumed excess space in the popup. Reduced them to compact revision/save information while retaining all medical input fields, canonical identity, warnings and history. Source patient/visit pickers are intentionally fixed to the current visit here; no patient switch or duplicate clinical record is created.
6. P2: mobile sort filter collapsed into a narrow reset-icon column. Gave long dropdowns separate wide tracks, recaptured mobile, and verified no page overflow.
7. User-requested override to original clinical template: removed manual visit status selection and displayed a system-owned status badge. All three visits tabs now remain on the same line, desktop and mobile. DOM measurements show all three at y=315 desktop and y=483 mobile.

### Required fidelity surfaces

- Typography: existing Tajawal/Inter retained; 24 px directory headings, compact 12 px table/toolbar text and 16 px drawer headings. Widths and overflow prevent dense data from collapsing into one-word columns.
- Layout/rhythm: original table, pill, toolbar and side-drawer composition restored; same soft slate borders, white surfaces and restrained rounded corners. Popup footer has safe bottom spacing and independent body scrolling at 390 px.
- Colors/tokens: existing brand blue, pale-blue inactive pills, emerald approved states and amber urgent states reused. Unsupported routing/sending states are explicitly disabled rather than presented as completed integrations.
- Assets/icons: approved THERA doctor-panel branding retained as previously requested, intentionally replacing the template's smaller legacy logo. Existing Material Symbols icon family retained.
- Copy/content: kept original page/column labels and real persisted records. External reports remain identified as external, never as THERA-certified results. Sending, dispensing and provider integrations are not fabricated. Existing extra medical fields/review safeguards remain available rather than being removed to imitate mock data.

### Functional verification

- Browser, isolated SQLite QA only: created/saved laboratory, prescription, referral and follow-up drafts from inside the visit; added/removed a prescription row; searched Select2 inside referral popup; approved radiology and verified read-only fields and print link.
- Verified parent unsaved clinical notes survived popup open, save and close; URL stayed on the visit. Existing canonical document links reopened the same records. Cancelling discard confirmation retained popup edits.
- Automatic prescription status filter replaced results and URL without a submit button; reset icon restored the list. Real diagnostic report filters and ownership/count isolation are feature-tested.
- System visit states: blank visit remains draft; saved documentation becomes in-progress; current approved orders without fully reviewed reports produce pending-results/review; reviewing all reports clears waiting, including superseded-order handling. Workflow-only updates preserve medical content and clinical revision to avoid invalidating the parent doctor's unsaved text. Closed visits are never reopened automatically.
- Client-supplied visit status is ignored; only explicit reviewed close completes the visit and appointment. Browser verified save without status, automatically displayed waiting state, then explicit close navigated to `/doctor/appointments?view=list` and showed the persisted success message.
- Tests: 582 PHP tests / 3795 assertions passed; 42 JavaScript tests passed. Pint fixed modified PHP files; `git diff --check` passed. One intermediate parallel PHP run collided on the shared fake-storage directory; the final complete suite ran by itself and passed.
- Console errors: none in the final tested visit, popup, prescription, laboratory, radiology and mobile surfaces.
- No writes were made to the user's port-8000 database; browser writes were confined to the pre-existing isolated SQLite QA database on 8011.

No remaining actionable P0/P1/P2 findings within this scope. Remaining differences are intentional: real clinical data, fixed visit identity, retained extra medical fields, unavailable external integrations, and automatic state selection explicitly requested by the user.

final result: passed

## Five doctor management directories — 2026-11-02

### Source and rendered evidence

- Visual truth: `C:/Users/Nour/Desktop/Thera/Thera_v2 Doctor Frontend/`: `Results Review.html`, `Referrals Followups.html`, `Doctor Schedule.html`, `Services Pricing.html`, and `Collections Dues.html`.
- Implementation: the corresponding existing doctor routes, rendered in the in-app browser against the isolated SQLite QA server on port 8011. The user's port-8000 database was not changed.
- Evidence directory: `C:/Users/Nour/AppData/Local/Temp/thera-fidelity-20261031/`.
- Desktop source captures: `results-original.png`, `referrals-original.png`, `schedule-original.png`, `services-original.png`, `collections-original.png`.
- Desktop implementation: `results-final.png`, `referrals-final.png`, `schedule-final.png`, `services-final.png`, `collections-final.png`; `services-empty-final.png` additionally exercises the empty-table state.
- Opened side-by-side comparisons: `results-comparison-final.png`, `referrals-comparison-final.png`, `schedule-comparison-final.png`, `services-comparison-final.png`, `collections-comparison-final.png`, and `services-empty-comparison.png`.
- Focused readable comparisons: `management-controls-focused-comparison.png` (breadcrumb, heading, metrics, tabs, filter controls, table headings) and `service-drawer-focused-comparison.png` (header, labels, fields, close control, footer).
- Mobile source/implementation pairs: `{results,referrals,schedule,services,collections}-mobile-comparison.png`. Additional drawer evidence: `service-drawer-mobile-final.png`, `schedule-drawer-final.png`, `schedule-exception-drawer-final.png`, `collections-drawer-final.png`.

### Viewport, density and state

- Desktop: normal approximately 1280 × 720 browser surface; source raster captures 1265 × 712, implementation 1265 × 712 except the short populated services page at 1280 × 720. The empty services comparison is 1265 × 712 on both sides. Browser scrollbar/client-frame differences are not treated as typography differences.
- Focused content comparisons use the same 949-pixel-wide desktop content crop without stretching. Images are 1× CSS density; no double-density downsampling was used for fidelity judgments.
- Mobile: 390 × 844 CSS viewport; comparable page screenshots returned 375 × 812 client rasters, and the full-width mobile drawer returned 390 × 844. Used viewport screenshots only. An initial full-page capture clipped content incorrectly despite correct DOM bounds; those captures were discarded and replaced before review.
- State: authenticated doctor; light theme; default directory filters; existing QA records for referrals, services, schedule and collections; empty results. The original templates have empty fixtures except sample collections. Actual record text, counts, active working days and real payment amounts intentionally differ. A separately filtered empty services state verifies the table skeleton and empty CTA against the original empty fixture.
- Source mobile templates leave the fixed desktop sidebar covering content. This defect is intentionally not copied: the implementation uses the existing mobile navigation and cards. Document widths were checked on all five routes: scroll width equals client width, with internal tab/table scrolling only.

### Comparison history and fixes

1. [P1] Results cards and referral rows did not reproduce the source tables. Replaced them with the original column order, compact metrics, status pills, table surfaces and mobile cards; preserved canonical links, report upload, medical review and immutable document/version behavior.
2. [P2] Large setup instructions displaced services/schedule above-the-fold content. Preserved their complete content and existing guide controls inside expandable help below the primary content. Guide navigation opens its containing disclosure when needed.
3. [P2] Schedule rows doubled the vertical density by adding an extra blank row after every working day. Moved the add-period action into the final existing period row; kept empty days, multiple periods, services, breaks, validation and save payload unchanged. Restored separate start/end columns and the exceptions table.
4. [P2] Finance filters formed an extra nested card. Restored the facility selector above the financial explanation, four compact metrics, and tabs/search/date/status/reset in the table header. Recaptured `collections-final.png` and its combined comparison after fixing specificity.
5. [P2] Drawer field scale and grouping differed from the source. Restored grouped service headings, light header/field surfaces, 14px field text, separated price section, opposite-side close control, and fixed padded footer. Verified the mobile footer within the 844px viewport.
6. [P2] Empty services initially placed its scrollbar between the headings and empty content. Moved the empty state into a spanning table cell, restored its muted circular icon and height, recaptured and reviewed `services-empty-comparison.png`. The secondary add button uses the same edit-to-create guard as the primary button; browser verified it opens a blank new-service form rather than editing the previous service.

### Required fidelity surfaces

- Fonts/typography: retained Tajawal and Inter from the supplied workspace; 24px desktop headings, 12px subtitles, 11px table headings, compact numeric metrics and readable field labels. Checked wrapping and full pill backgrounds rather than clipping labels.
- Spacing/layout: source 24px desktop gutters, 12px card gaps, 20px section rhythm, compact metrics, 12–16px radii, 40px filter controls and the weekly-table/capacity split. Checked headers, toolbars and drawer footers at desktop and mobile sizes.
- Colors/tokens: source brand blue, pale slate page/table surfaces, neutral borders and green/amber/purple metric accents. Real draft/approved/reviewed/payment states remain semantic, rather than relabeling records to match fixture text.
- Images/icons: retained the supplied THERA logo and approved “لوحة الطبيب” lockup; reused Material Symbols, not drawn or generated substitutes. This intentionally preserves the user's newer logo requirement rather than the old tiny template logo.
- Copy/content: real owned records, accurate external-report provenance and unavailable integration labels. No invented execution, routing, deposit or settlement values; unsupported prototype-only controls remain unavailable. Existing membership/date filters and multi-period schedule editing remain available even where the static prototype is simpler.

### Functional verification

- Browser: automatic finance search and reset; collection drawer opening and URL cleanup after close; service edit/save and persisted instructions; schedule period editor/apply/save and persisted success; top exception action; Select2 search and oldest-first referral order; follow-up tab returning the single matching record; empty-state new-service action clearing previous edit context.
- Mobile: all five pages plus full-width service drawer; labels, close control and padded footer remain visible; no page-wide horizontal overflow. No console errors in final tested directory/drawer states.
- Automated: complete PHP suite passed, 603 tests / 3910 assertions. Following the final view-only changes, the focused management suite passed again (14 tests / 62 assertions); the schedule/collection/management group also passed (96 tests / 461 assertions). All 42 JavaScript tests passed after the create-button binding change. Modified PHP files passed Pint; JavaScript syntax checks passed; `git diff --check` has only the pre-existing unrelated CRLF warning for verification.blade.php.
- Existing write controllers, authorization, revisions, upload validation, financial ordering/calculations and AJAX endpoints were retained. Backend additions are read-only, validated sorting and doctor-scoped referral/follow-up counts, covered by feature tests.

No actionable P0/P1/P2 findings remain within this scope. Expected adaptations: real content, unsupported integrations, retained multi-period editing and additional operational filters, approved logo branding, and responsive fixes to the original mobile template. P3: minor natural row-height differences with longer real names/copy.

final result: passed

## 2026-11-02 — Account settings, professional profile and shared doctor shell

Functional implementation verified; exact full-template visual sign-off remains blocked.

- Sources: `C:/Users/Nour/Desktop/Thera/Thera_v2 Doctor Frontend/Doctor Settings.html`, `Professional Profile.html`; supplied footer screenshot `C:/Users/Nour/AppData/Local/Temp/codex-clipboard-cf0ee009-7439-424c-ac28-f8e4abd8643e.png`.
- Current captures: `C:/Users/Nour/AppData/Local/Temp/thera-fidelity-20261031/settings-final-light-20261102.png`, `profile-final-light-20261102.png`, `profile-final-dark-20261102.png`, `settings-mobile-notifications-20261102.png`, `verification-mobile-account-20261102.png`.
- Desktop viewport 1265 × 712 CSS px; mobile viewport 390 × 844 CSS px. Captures use matching 1× CSS/pixel dimensions; no screenshot rescaling. The supplied footer is a cropped source, not a full-page template capture.
- The provided footer and current settings screenshot were opened together for focused comparison. Retained dark footer, circular initial avatar, status dot, stacked name/title/status and overflow action; live account data intentionally differs from the sample.
- Full source/implementation screenshot comparison is unavailable: browser security policy denied navigation to local `file:` HTML. No alternate hosting or browser workaround was attempted. Template CSS/markup were compared as source code only; this is not a pixel-accurate visual sign-off.

### Changes and verification history

1. Removed duplicate profile/verification shell handlers and replaced verification's static sidebar/topbar with shared authenticated partials. Fixed duplicate workspace runtime includes in services and schedule. Links now use named server routes; browser verified only one active sidebar link.
2. Restored template settings grid (245px navigation, 680px minimum height), compact 24px/18px headings, grouped 16px-padded rows, profile cards, and horizontal section navigation on mobile. Preserved existing forms, revisions, validation, policy switches and account isolation.
3. Enabled doctor-scoped operational reminders based on actual records and stored notification preferences, without exposing patient details. No invented unread messages, delivery history, external sending or realtime push claims.
4. Added light/dark/device appearance and Arabic/English menu/static-heading preferences scoped by doctor and device. This is not a complete English translation of every page or server message. Authored medical records and names are not translated or sent externally.
5. Browser iteration found all sidebar links initially highlighted due to placeholder links: fixed with named routes and retested all primary directories. Found low-contrast dark verification cards: added verification/clinical/patient surface rules and Bootstrap dark theme. Found clipped translated profile tabs: kept Arabic proportional grid and made English tabs scrollable without shrinking. Found inline mobile doctor metadata: restored stacked flex metadata. Retested shared mobile sidebar on verification.
6. Browser verified the five settings sections and seven profile tabs; account menu on 13 primary directories, new appointment, appointment detail, patient detail, clinical visit, professional profile and verification; notification dropdown data; language/theme persistence; mobile navigation and popovers. No console errors in the final checked states. No page-wide horizontal overflow in the checked directory/detail states.
7. Automated: full PHP suite 640 tests / 4205 assertions passed; focused shared-shell suite rerun after final verification stylesheet change: 19 tests / 177 assertions. JavaScript menu, theme, language, failure/retry, safe search and settings dirty-state tests passed. Pint passed; JavaScript syntax and diff whitespace checks passed (existing CRLF warnings only).

### Fidelity surfaces and limits

- Typography: retained template Tajawal/Inter and Material Symbols; restored settings title, section and navigation sizes. No replacement imagery or icon system.
- Spacing: preserved profile layout and existing responsive tab fixes; settings grid/row measurements follow the original source CSS. Exact screenshot-to-template spacing comparison remains unverified.
- Colors: original light palette retained; dark surfaces are an explicit functional extension, not claimed as an original dark reference match. Semantic status colors remain distinct.
- Assets: existing approved THERA lockup and real account initials retained; no generated substitutes.
- Copy: real account data and honest operating/reminder states. English coverage is intentionally documented as partial.

Remaining blocker: full-page reference screenshots are needed to complete 1:1 visual comparison of every settings/profile section. No claim of full template equivalence or complete English localization.

final result: blocked

## Credentials, achievements and saved doctor payment policy — 2026-11-02

Follow-up implementation uses the original `Professional Profile.html` cards/dialog markup and `Doctor Settings.html` payment section. The earlier rendered-source access restriction still applies; no alternate-serving workaround was used.

- Fixed malformed credential card markup: title, issuer, country, year and status are inside the card again; detail dialogs are siblings rather than accidentally containing card content in their footer. Added populated DOM assertions for both professional item types.
- Restored credential document preview plus metadata sidebar and verified credential detail banner/table. Image/PDF previews use the authenticated ownership-checked private endpoint, an explicit MIME allowlist, inline disposition, no-store and nosniff headers. Downloads and permitted deletion remain available.
- Restored achievement type/status chips, card spacing, detail title/description panel, metadata rows and evidence action. Public visibility still requires the actual verified state.
- Replaced the previous generated payment design with the original three payment-mode cards, editable percentage, remaining-payment timing cards, early-payment switch and live example. Saved percentages are doctor-scoped integers from 1 to 99; full payment is its separate 100% mode. Disabled fields retain their saved values. Revision conflicts, retry protection and authorization remain intact.
- The UI explicitly distinguishes saved preferences and current manual collection from unavailable electronic charging and service-specific overrides. No gateway, charges, historical price changes or per-booking snapshot implementation is claimed.

### Browser evidence

User explicitly approved one synthetic credential and one synthetic achievement in the isolated SQLite QA database only. Both were created there; the real account database was not modified. A private placeholder image was used solely as QA evidence.

- Pending credential card and real private document preview were opened successfully. Achievement detail content and evidence link were checked populated.
- Desktop and 390×844 mobile screenshots were inspected. Mobile achievement dialog footer bottom is 843px; document width is 375px within the 390px viewport. Dark-mode title, metadata and footer remain readable.
- Payment percentage 35 was saved through the existing AJAX flow, persisted after reload, then restored to the QA account's original 10. Timing and early-payment values were preserved. All three payment modes and disabled controls were exercised.
- Browser console error checks returned empty. Theme and viewport overrides were restored after QA.
- Evidence directory: `C:/Users/Nour/AppData/Local/Temp/thera-fidelity-20261031/`; files: `credential-document-final.png`, `achievement-details-final.png`, `achievement-mobile-final.png`, `achievement-mobile-dark-final.png`, `payment-mobile-final.png`, `payment-policy-final.png`.

### Verification and remaining limits

Full PHP suite: 668 tests / 4430 assertions passed. JavaScript suite: 50 tests passed. Pint passed. Functional and implementation-view QA passed; exact pixel-level fidelity remains unverified because paired rendered-source screenshots are unavailable under the existing browser restriction. The earlier blocked fidelity sign-off is not overridden by this functional pass.

final result: blocked (paired source-image fidelity only; implementation and functional verification passed)

## 2026-11-02 — Selected payment-policy design, displayed option 1

This review concerns only the newly selected payment-policy redesign, not the earlier full settings/profile template audit.

### Reference and rendered evidence

- Selected source: `C:/Users/Nour/.codex/generated_images/01a12be6-56d6-7f92-bc26-565c600c6b78/exec-8ab54f60-73ce-444a-b756-5f89d4ef7e82.png` (first displayed image, 1586 × 992 pixels).
- Final rendered screenshot: `C:/Users/Nour/AppData/Local/Temp/thera-fidelity-20261031/payment-option-one-desktop-final.png`. Browser viewport 1854 × 1002 CSS px, 1× density; the app's existing 268px global sidebar is retained rather than recreated from the concept. Compared the payment/local-navigation region against the supplied concept, not the unrelated global app shell. Both source and final screenshot were opened together in the same comparison input.
- Mobile: `payment-option-one-mobile.png`, `payment-option-one-mobile-dark.png`, `payment-option-one-mobile-footer.png` in the same capture directory, 390 × 844 CSS px. Tablet checked at 820 × 1100. No page-wide overflow or clipped payment sections in browser DOM measurements.
- Rejected intermediate clipped/resized browser captures; final proof uses an uncropped viewport, scrolled to show the entire payment section and footer. The reference's switch is on; final QA account switch is off because its actual original setting was restored after testing both states. No production preference was changed to match a mock.

### Comparison history and fixes

1. Initial layout had compact text and a shared outer navigation border, unlike the selected concept. Kept the blue policy area, separated the reference percentage/caution, and split manual collection/future integrations into clear divided sections.
2. Increased wide-screen text scale, aligned the switch/title, matched the on/off thumb direction and tuned paragraph/row spacing. Retained the exact original switch name, hidden false input, ID, descriptive paragraph, old-input handling, revision field, form action and AJAX save.
3. Separated the content panel and local settings navigation with a 14px gap and 18px rounded borders for this active section only. Other settings tabs retain their existing layout. Final full-view comparison verifies the selected hierarchy, blue switch surface, percentage badge, amber caveat, supporting text/link, and bottom save/status area.

### Required fidelity surfaces

- Typography: existing Tajawal/Inter retained, with clear heading/body/caption hierarchy and readable line heights; responsive sizes adapt to the narrower actual app frame. Text is not rasterized. Minor glyph/weight differences from generated lettering are expected P3, not missing content.
- Spacing/layout: matched separate navigation/content surfaces, pale-blue policy strip, aligned reference summary/caution, divided supporting rows, and padded footer. Mobile stacks the financial summary and keeps buttons full-width; local navigation scrolls rather than clipping sections.
- Colors: blue `#0495f6`, white/pale slate surfaces, muted headings and warm caveat. Dark mode uses existing account-scoped theme conventions with separate readable blue/amber surfaces.
- Images/icons: no raster assets needed for this form. Reused the app's Material Symbols and native Bootstrap switch; no invented logo or custom illustration.
- Content: every original policy/financial statement remains visible. Deposit is still read from configuration (test verifies 17%, not hard-coded mock 10%). No new payment integration, commission, default policy, or permission change was introduced.
- Focused control evidence: switch/paragraph association was checked through rendered DOM and accessible state; mobile footer capture confirms readable content and padded save/link controls. Full-resolution paired views make section labels, value, note and icons readable without another source crop.

### Functional verification

- Browser: toggled and saved ON with existing AJAX/SweetAlert flow, reloaded to verify persistence; saved OFF and reloaded again, restoring the QA account's initial value. Original real database/account settings were not used for writes. Console error check returned empty.
- Full automated suite: 642 PHP tests / 4233 assertions and 48 JavaScript tests passed. Focused settings/policy/shell group: 53 tests / 387 assertions passed. Pint and whitespace checks passed (existing unrelated CRLF warnings only).
- PHP changes are test coverage only. No controller, query, validation, authorization, queue, scheduling or financial calculation was modified for this redesign.

No actionable P0/P1/P2 issues remain in this selected payment-section implementation. Expected adaptations: retained global app chrome/top save shortcut, live switch state/configuration value, responsive stacking and dark-mode extension. Earlier full-template/complete-localization limits remain outside this scope.

final result: passed

## Professional profile template restoration — 2026-11-02

Source: `C:/Users/Nour/Desktop/Thera/Thera_v2 Doctor Frontend/Professional Profile.html` (HTML, CSS and prototype modal markup inspected).

Implementation: `/doctor/account/professional-profile`, all seven tabs. Browser QA used the isolated SQLite review database on port 8011; no live account records were changed.

### Changes and verification

- Restored template spacing instead of Bootstrap's conflicting spacing scale, Tailwind grids/breakpoints, 40px controls, rounded pills and card hierarchy. All seven tabs remain connected to their panels and history.
- Restored basic/professional field layout and experience unit positioning. Primary specialty editing moved into a real dialog; existing assignment selection, Select2 search, locked verified values, validation reopening and POST action remain intact.
- Credentials and achievements use template card/detail-dialog presentation; actual private evidence downloads and authorized deletion remain available in those dialogs. File upload areas use the template dashed presentation with accessible native file inputs and safe filename text.
- Restored facility two-column cards/detail dialog and public-preview 330px aside, spacing, content order and service-count/link card. Preserved membership requests, visibility controls, real data and verified/public-only filtering. No fake records or prototype actions were connected.
- Preserved the previously approved shared doctor shell/logo, real account data and required controls absent from the static prototype. No controllers, database schema, validation or approval rules changed.

### Evidence and fidelity surfaces

- Desktop CSS viewport: 1366×900; mobile: 390×844. Captures are uncropped browser viewport PNGs. No source pixel-density normalization or paired rendered-source comparison was possible.
- Implementation screenshots: `C:/Users/Nour/AppData/Local/Temp/thera-fidelity-20261031/profile-basic-template-final-20261102.png`, `profile-preview-template-20261102.png`, `profile-modal-mobile-template-20261102.png`, `profile-modal-mobile-dark-template-20261102.png` in the same directory.
- Typography: existing Tajawal/Inter and Material Symbols retained; source heading/label/body hierarchy inspected in code and live rendering.
- Layout: desktop tab/field/card alignment inspected, mobile has no document horizontal overflow, and dialog footer bottom is 843px within the 844px viewport.
- Colors: restored template blue/state borders; found and fixed white dialog headers/footers in dark mode, then recaptured the dark dialog with readable title and actions.
- Assets: existing official brand and icon font retained; no new decorative assets or logo approximations.
- Content: live professional data retained rather than static sample values. Private uploads remain private and public preview filtering is unchanged.
- Browser interactions: all seven tabs, primary-specialty dialog/Select2 search, facility details/proposal dialog, credential/achievement addition dialogs, close controls and mobile theme tested. Browser console returned no errors. The isolated review account has no credential/achievement rows; populated detail rendering/actions are covered by feature tests, not browser screenshots.
- Automated: 74 relevant PHP tests / 496 assertions, 48 JavaScript tests, Pint and scoped whitespace checks passed.

### Remaining blocker

Exact rendered-template fidelity is not signed off. The browser previously explicitly blocked opening the local source `file://` page and prohibited alternate-serving workarounds. Source code was readable, but a corresponding source screenshot for every tab/modal at these viewports is unavailable. Full-view and focused paired-image comparison therefore remain blocked; do not describe this work as a verified 100% visual match. Supply reference screenshots or an accessible approved source preview to complete that comparison.

final result: blocked
## Settings professional summary and native icon navigation — 2026-11-03

Source: `C:/Users/Nour/Desktop/Thera/Thera_v2 Doctor Frontend/Doctor Settings.html`, `section-profile` (lines 456–477). Existing HTML was inspected; the user screenshot depicts the old implementation, not the desired source state. The previously recorded browser prohibition on opening the source file remains in force. No alternate-serving workaround was attempted.

- Restored the source heading/caption, pale verification notice with icon, four summary cards (name, specialty, facilities and services), and three bordered management rows with their source descriptions.
- Used existing Tajawal/Material Symbols, 18/14/12px hierarchy, 16px card/row padding, 12px gaps/radii, and two-column summary at 768px. Contact details remain available on the name-card tooltip; real account data is unchanged. Services are eager-loaded and limited to active owned memberships/services.
- Replaced text buttons with 36px icon links, accessible names, titles, focus outlines and native `_self` navigation. Mobile targets expand to 44px. Native destination loading avoids carrying settings CSS into the profile/verification pages.
- Browser QA: all three actual icon links clicked successfully; the achievements destination selected its correct tab. Profile and verification destination documents do not retain `doctor-settings.css`. Browser console errors: none. No business data was written; QA account only.
- Desktop capture: `C:/Users/Nour/AppData/Local/Temp/thera-fidelity-20261031/settings-profile-template-final.png` (1280×720 viewport). Mobile captures: `settings-profile-mobile-final.png` and `settings-profile-dark-mobile-final.png` in the same directory (390×844). Mobile document width 375px, no horizontal overflow; all three icon targets 44×44px. Theme and viewport restored.
- Colors and states use the source emerald/border/slate palette, with a truthful pending state and a scoped dark-mode extension. No raster assets were needed; existing supplied brand assets were retained. Source sample text is replaced only by live values and functional accessible icon actions requested by the user.
- Functional tests cover four-card structure, owned active service filtering, actual destination responses/assets, icon-only accessible links, and avoiding AJAX interception. Settings tests: 32 passed / 197 assertions.

Exact paired-image/source-density comparison is still unavailable: a rendered source screenshot is required for pixel-level sign-off. Functional and implementation-view QA passed; this is not a claim of verified 100% visual fidelity.

final result: blocked
