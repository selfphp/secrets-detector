# 🔐 secrets-detector

Detect hardcoded secrets like API keys, tokens or passwords in PHP projects – ideal for CI/CD pipelines, pre-commit hooks, or manual audits.

## 🚀 Features

- 🔍 Scans `.env`, `.ini`, PHP source and config files for secrets
- 📦 Composer integration (autoload & CLI)
- 🧠 Framework-aware: supports Symfony (`APP_SECRET`), Laravel (`JWT_SECRET`), and others
- ⚙️ Configurable scan paths via `.secrets-detector.json`
- 📄 Output formats: JSON, Markdown
- ✅ CI/CD-ready with exit codes for automated fail conditions

## 📦 Installation

### As a dev dependency:

```bash
composer require --dev selfphp/secrets-detector
```

### Or install globally:

```bash
composer global require selfphp/secrets-detector
```

## 🔧 Usage

```bash
php bin/secrets-detector secrets:scan
```

### Options

| Option              | Description                                  |
|---------------------|----------------------------------------------|
| `--json=report.json`     | Export results to JSON                   |
| `--markdown=report.md`  | Export results to Markdown               |
| `--fail-on-detect`      | Exit with non-zero code on finding secrets |

### Example

```bash
php bin/secrets-detector secrets:scan --json=report.json --markdown=report.md --fail-on-detect
```

## 🛠 Configuration

You can define custom include/exclude paths using a `.secrets-detector.json` file in your project root:

```json
{
  "include": ["src", "config", ".env"],
  "exclude": ["vendor", "tests"]
}
```

## 🧪 Testing

Run all unit tests:

```bash
vendor/bin/phpunit --display-deprecations
```

## ✅ CI Integration

See ready-made CI examples in [docs/ci](docs/ci):

- `github-actions.yml`
- `gitlab-ci.yml`
- `bitbucket-pipelines.yml`

Each file shows how to run the CLI and fail builds if secrets are detected.

## 🧠 Patterns Detected

- Stripe secrets: `sk_live_`, `sk_test_`
- Bearer tokens: `Authorization: Bearer ...`
- AWS credentials: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`
- Database logins: `DB_PASSWORD`, `DB_USERNAME`
- JWT and app secrets: `JWT_SECRET`, `APP_SECRET`
- Google API keys: `AIza...`
- Private keys: `-----BEGIN PRIVATE KEY-----`

(See [PatternRegistry](src/Pattern/PatternRegistry.php) for all patterns.)

## 📜 License

MIT

## 👤 Author

**SELFPHP - Damir Enseleit**  
[https://www.selfphp.de](https://www.selfphp.de)  
[@SELFPHP](https://phpc.social/@SELFPHP)
