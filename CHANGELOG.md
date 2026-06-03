# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-06-03

### Added
- `taskToken` field on `SlackMessageDto` (WEB-10965)
- `SlackFileMessageDto` DTO for file upload messages (WEB-10967)
- `FileUpload` queue support for routing file upload messages (WEB-10967)

## [1.0.0] - 2026-05-24

### Added
- `SlackGatewayPublisher` — publishes messages to a Slack gateway via AWS SQS
- `SlackGatewayPublisherInterface` — interface for type-hinting and mocking in consuming apps
- `SlackMessageDto` — readonly DTO carrying channel, text, blocks, attachments, thread timestamp, idempotency key, and source
- `SlackQueue` enum — `High`, `Normal`, and `Low` priority cases; queue names derived as `{prefix}-high`, `{prefix}-normal`, `{prefix}-low`
- Laravel service provider with auto-discovery support
- Symfony bundle with `SlackGatewayClientExtension` and `Configuration`
- PHPUnit test suite covering publisher behaviour and Symfony DI wiring
- GitHub Actions CI across PHP 8.2, 8.3, and 8.4
