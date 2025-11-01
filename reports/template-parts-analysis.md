# Template Parts Analysis Report
**Date**: 2025-10-31  
**Scope**: Analysis of template-parts usage and broken references

---

## Current Template Parts

### Existing Files (3 files)

```
template-parts/
├── front-page/
│   ├── section-hero.php          (55KB)
│   └── section-search.php        (93KB)
└── grant-card-unified.php        (70KB)
```

---

## Usage Analysis

### ✅ Template Parts in Use

#### 1. `template-parts/front-page/section-hero.php`
**Status**: ✅ **IN USE**

**Referenced by**:
- `front-page.php:120`
  ```php
  get_template_part('template-parts/front-page/section', 'hero');
  ```

**Purpose**: Front page hero section with main headline and CTAs

---

#### 2. `template-parts/front-page/section-search.php`
**Status**: ✅ **IN USE**

**Referenced by**:
- `front-page.php:132`
  ```php
  get_template_part('template-parts/front-page/section', 'search');
  ```

**Purpose**: Front page search functionality section

---

#### 3. `template-parts/grant-card-unified.php`
**Status**: ✅ **IN USE**

**Referenced by** (5 locations):
1. `archive-grant.php:1228`
2. `inc/ajax-functions.php:3327`
3. `inc/ajax-functions.php:4190`
4. `index.php:176`
5. `taxonomy-grant_tag.php:657`

**Purpose**: Unified grant card display component used throughout the site

---

## 🚨 Broken References Found

### Missing Template Parts

#### 1. ❌ `template-parts/grant-card-list-portal.php` - **MISSING**

**Referenced by** (2 locations):
1. `taxonomy-grant_municipality.php:841`
   ```php
   get_template_part('template-parts/grant-card-list-portal');
   ```

2. `taxonomy-grant_prefecture.php:627`
   ```php
   get_template_part('template-parts/grant-card-list-portal');
   ```

**Status**: 🚨 **FILE DOES NOT EXIST**

**Impact**: 
- WordPress will silently fail to load this template part
- Grant cards may not display correctly on municipality/prefecture taxonomy pages
- No error thrown (WordPress behavior)

**Likely Cause**: 
- File was deleted in previous cleanup (possibly Round 3)
- Or renamed to `grant-card-unified.php`
- References not updated

**Recommended Fix**: 
Replace with existing `grant-card-unified.php`:
```php
// Change this:
get_template_part('template-parts/grant-card-list-portal');

// To this:
get_template_part('template-parts/grant-card-unified');
```

---

#### 2. ❌ `search.php` - **MISSING**

**Referenced by**:
- `index.php:17`
  ```php
  get_template_part('search');
  ```

**Status**: 🚨 **FILE DOES NOT EXIST**

**Impact**:
- Search fallback functionality broken in index.php
- May cause search results page to fail

**Recommended Fix**: 
Remove this line or create search.php if search functionality is needed:
```php
// Option 1: Remove the line if not needed
// get_template_part('search');

// Option 2: Use WordPress search template
// get_search_form();
```

---

#### 3. ⚠️ `404.php` reference in page-purpose.php

**Referenced by**:
- `page-purpose.php:141`
  ```php
  get_template_part('404');
  ```

**Status**: ⚠️ **INCORRECT USAGE**

**Issue**: 
- `404.php` exists as a full template, not a template-part
- This line attempts to load it as a template-part (wrong usage)
- Should use WordPress functions instead

**Current Code**:
```php
// 404 if purpose not found
if (!$current_purpose) {
    status_header(404);
    get_template_part('404');  // ❌ Wrong usage
    exit;
}
```

**Recommended Fix**:
```php
// Option 1: Load 404 template properly
if (!$current_purpose) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part('404');  // or include(locate_template('404.php'))
    exit;
}

// Option 2: Redirect to 404 page
if (!$current_purpose) {
    wp_redirect(home_url('/404'));
    exit;
}
```

---

## Summary of Issues

| Template Part | Status | Referenced By | Issue |
|--------------|--------|---------------|-------|
| `template-parts/front-page/section-hero.php` | ✅ OK | front-page.php | None |
| `template-parts/front-page/section-search.php` | ✅ OK | front-page.php | None |
| `template-parts/grant-card-unified.php` | ✅ OK | 5 files | None |
| `template-parts/grant-card-list-portal.php` | 🚨 **MISSING** | 2 taxonomy files | **File not found** |
| `search.php` | 🚨 **MISSING** | index.php | **File not found** |
| `404.php` | ⚠️ Misused | page-purpose.php | **Incorrect usage** |

---

## Impact Assessment

### Critical Issues: 2

1. **Missing `grant-card-list-portal.php`**
   - **Severity**: HIGH
   - **Affected Pages**: Municipality and Prefecture taxonomy pages
   - **User Impact**: Grant cards may not display
   - **Fix Complexity**: LOW (simple find & replace)

2. **Missing `search.php`**
   - **Severity**: MEDIUM
   - **Affected Pages**: Search results (index.php fallback)
   - **User Impact**: Search functionality may be broken
   - **Fix Complexity**: LOW (remove line or create search template)

### Minor Issues: 1

3. **Misused `404.php` reference**
   - **Severity**: LOW
   - **Affected Pages**: page-purpose.php (error handling)
   - **User Impact**: 404 error page may not load correctly
   - **Fix Complexity**: LOW (update function call)

---

## Recommended Actions

### Immediate Fixes Required

#### 1. Fix Missing `grant-card-list-portal.php` (HIGH PRIORITY)

**Files to update**:
- `taxonomy-grant_municipality.php` (line 841)
- `taxonomy-grant_prefecture.php` (line 627)

**Change**:
```php
// Before:
get_template_part('template-parts/grant-card-list-portal');

// After:
get_template_part('template-parts/grant-card-unified');
```

**Testing**: 
- Visit any municipality taxonomy page (e.g., `/grant_municipality/tokyo-shibuya/`)
- Visit any prefecture taxonomy page (e.g., `/grant_prefecture/tokyo/`)
- Verify grant cards display correctly

---

#### 2. Fix Missing `search.php` (MEDIUM PRIORITY)

**File to update**:
- `index.php` (line 17)

**Option A - Remove if not needed**:
```php
// Remove this line:
// get_template_part('search');
```

**Option B - Use WordPress search form**:
```php
// Replace with:
get_search_form();
```

**Testing**:
- Visit index.php page
- Verify no PHP warnings/errors
- Check if search form appears (if Option B chosen)

---

#### 3. Fix `404.php` Usage (LOW PRIORITY)

**File to update**:
- `page-purpose.php` (line 141)

**Updated code**:
```php
if (!$current_purpose) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    include(locate_template('404.php'));
    exit;
}
```

**Testing**:
- Visit invalid purpose URL (e.g., `/purpose/invalid-purpose/`)
- Verify 404 page displays correctly

---

## Verification Commands

### Check for broken references:
```bash
# Find all get_template_part calls
grep -rn "get_template_part" --include="*.php" . | grep -v "Binary"

# List existing template-parts
find template-parts -type f -name "*.php"

# Check if referenced files exist
ls -lh template-parts/grant-card-list-portal.php  # Should fail
ls -lh search.php  # Should fail
ls -lh 404.php  # Should exist
```

---

## Conclusion

**Status**: ⚠️ **BROKEN REFERENCES FOUND**

- ✅ **3 template parts** are correctly used
- 🚨 **2 critical broken references** require immediate fixes
- ⚠️ **1 incorrect usage** should be corrected

**Priority**: 🔴 **HIGH** - Fix broken references to prevent display issues

**Estimated Fix Time**: 15 minutes (simple find & replace)

---

## Next Steps

1. ☐ Update `taxonomy-grant_municipality.php` line 841
2. ☐ Update `taxonomy-grant_prefecture.php` line 627
3. ☐ Fix or remove `search.php` reference in `index.php` line 17
4. ☐ Correct `404.php` usage in `page-purpose.php` line 141
5. ☐ Test all affected pages
6. ☐ Commit fixes

---

**Report Date**: 2025-10-31  
**Files Analyzed**: 11 PHP template files  
**Issues Found**: 3 (2 critical, 1 minor)  
**Action Required**: Yes
