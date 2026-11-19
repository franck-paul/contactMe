# Changelog

## 10.0.1 — 2026-11-15

- Fix checking of contactme frontend variable, fix #14

## 10.0 — 2026-11-08

- Use getter methods rather than widget properties as far as possible
- Cope with adminSimpleMenuBeforeEditV2 behavior instead of adminSimpleMenuBeforeEdit, 2.40+
- Use Html/Form/Note rather than Para when it is applicable
- Use typed getter/setter Cursor method, DC 2.40+
- Enforces some verification before registering module

## 9.0 — 2026-08-03

- To soon for that!
- Avoid using magic methods for user preferences and blog settings (2.39+)
- Fav Icon
- Avoid using deprecated Auth::getOption()
- Review icon colors (DC 2.39+)

## 8.10 — 2026-05-15

- Add support of glaz template set
- Use dotclear.ToolBar rather than old jsToolBar, DC 2.39+

## 8.9 — 2026-04-10

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 8.8 — 2026-04-07

- Add class="content-info" on header, closes #13

## 8.7 — 2026-04-07

- Add class="buttons" on p containing submit button, closes #12

## 8.6 — 2026-02-16

- Add public URL info on management page, addresses #9

## 8.5 — 2025-10-12

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 8.4 — 2025-10-11

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 8.3 — 2025-09-22

- Use backend services rather than static calls

## 8.2 — 2025-09-12

- Replace static call to Ctx by context() — thanks JcDenis for advice
- Use array rather than ArrayObject for new ArrayObject value of simpleMenu choice

## 8.1 — 2025-09-07

- DC 2.36 compliance

## 8.0 — 2025-09-06

- DC 2.36 compliance

## 7.2 — 2025-06-01

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 7.1 — 2025-05-05

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 7.0 — 2025-04-16

- Use TemplateHelper for template blocks/values DC 2.34+

## 6.1 — 2025-02-26

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 6.0 — 2025-01-09

- 2.33 compliance

## 5.6 — 2024-11-12

- Switch from jQuery to Vanilla JS

## 5.5 — 2024-09-10

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.4 — 2024-04-06

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.3 — 2023-10-27

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.2 — 2023-10-25

- Simplify template dir management

## 5.1 — 2023-10-25

- Use template()-&gt;appendPath(…) rather than -&gt;setPath(-&gt;getPath(), …)

## 5.0 — 2023-10-24

- Use instanced item constants rather than interface ones
- Avoid magic method as far as possible
- Use 2.28 Config methods
- 2.28 compliance

## 4.0 — 2023-08-23

- Dotclear 2.27 compliance, PHP 8.1+

## 3.1.2 — 2023-07-24

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 3.1.1 — 2023-06-27

- Add local static $init variable

## 3.1 — 2023-06-20

- Use dcCore::app()-&gt;adminurl-&gt;redirect() rather than Http::redirect()

## 3.0.2 — 2023-06-14

- Don't do things in frontend if plugin disabled

## 3.0.1 — 2023-05-25

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 3.0 — 2023-05-25

- Switch to namespace
- Prepare 2.26
- Remove addNamespace() call, deprecated
- Prepare 2.25

## 2.1.1 — 2023-02-03

- Fix addNamespace() call
- Fix define
- Minified version
- Move js in folder

## 2.1 — 2022-12-18

- Update index.php

## 2.0 — 2022-11-28

- Avoid notice on new installation

## 1.16 — 2022-11-22

- 2.24 compliance

## 1.15 — 2022-08-15

- Update default branch in _define.php for alternative repository
- Add alternative repository

## 1.14 — 2021-08-06

- Dotclear 2.19 compliance

## 1.13 — 2021-03-10

- corrections linguistiques

## 1.12 — 2021-03-06

- Add publicContactMeFormBeforeContent and publicContactMeFormAfterContent behaviors, closes #4

## 1.11.1 — 2020-12-29

- fixed bug when SMTP account is specified and there is a : in the blog name

## 1.11 — 2020-09-17

- Add SMTP account option (used as From: header if any), add ReplyTo: header
- jQuery 3+ compliance

## 1.10.1 — 2020-06-06

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 1.10 — 2020-06-06

- Add activation flag, should fix #1

## 1.9.1 — 2020-06-02

- Add required and aria-required attributes to mandatory fields, a little step for a11y

## 1.9 — 2020-02-28

- Not too fast dude!
- Adapt widget code, add support URL
- Cope with new static home page mode (2.16+)
- short notation for array (array() → [])

## 1.8.1 — 2017-02-03

- Don’t forget editor context
- Cope with Rich text editor

## 1.8 — 2016-12-28

- Remove Dublin-core meta, XHTML optional wysiwyg editor

## 1.7 — 2016-08-16

- Add dotty templateset (HTML5), next release will be 1.7
- Do not allow empty recipient list (admin)
- Add rich editors to textarea fields

## 1.6.1 — 2016-06-01

- Using dcPage::cssLoad, dcPage::jsLoad and dcPage::getPF is a better way to load plugin resources

## 1.6 — 2015-06-27

- Deal with updated mustek templateset, min version for dotclear will be 2.8
- Add {{tpl:Breadcrumb}} tag in mustek template, next release will 1.6 (Dotclear 2.8 min)

## 1.5 — 2014-11-14

- Add "offline" option on widgets
- Cope with templaset (mustek + currywurst), switch to HTML5
- Add widget class on every widget, see #1901
- New rendering method for widget title (2.7 min), next release will be 1.5

## 1.4.1 — 2013-10-18

- Various

## 1.4 — 2013-10-15

- new _define.php
- Cope with new favorites system
- New notices system
- new icons and fav
- Cope with new breadcrumb
- No more $GLOBALS at top level, and various
- 2.6 compliance
- various (next release will be 1.4)
- Autres correctifs et améliorations.

## 1.3 — 2013-03-16

- Version 1.3
- Widget may now be displayed without inclusive div
- harmonize &lt;label&gt;&lt;/label&gt; in forms
- Now uses core message function (dcPage::message)
- Add widgets description
- Add missing files
- Revert to original name and desc in define.php of plugins. Localized versions are not required here.
- New display mode (except on home page) for widget
- Autres correctifs et améliorations.

## 1.2 — 2011-07-19

- Breadcrumb compliance
- simpleMenu compliance, step 2
- simpleMenu compliance
