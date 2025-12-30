# GitHub Actions Workflows

This directory contains CI/CD workflow configurations for the iRequest Demo project.

## Workflows

### CI Workflow (`.github/workflows/ci.yml`)

**Triggers:**
- Push to `main`, `master`, or `develop` branches
- Pull requests to `main`, `master`, or `develop` branches

**Jobs:**
- **Test**: Runs PHPUnit tests on multiple PHP versions (7.4, 8.0) on Ubuntu

**Steps:**
1. Checkout code
2. Setup PHP with required extensions
3. Copy environment file
4. Install Composer dependencies
5. Generate application key
6. Set directory permissions
7. Run Laravel tests

**Test Environment:**
- Uses SQLite in-memory database for testing
- No external dependencies required
- Fast execution time

