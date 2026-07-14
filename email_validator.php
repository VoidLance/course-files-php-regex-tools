<?php
// Student note: this function validates one email and returns if it is valid + why.
function validateEmail($email, $strictMode = false)
{
	$email = trim((string) $email);

	if ($email === '') {
		return ['valid' => false, 'reason' => 'This line is empty.'];
	}

	// First big regex: username + @ + domain + TLD(2-63 chars).
	$fullPattern = '/^[A-Za-z0-9._-]+@(?:[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?\.)+[A-Za-z]{2,63}$/';

	if (preg_match($fullPattern, $email) !== 1) {
		// If the main regex fails, we run smaller checks so we can explain why.
		if (preg_match('/^[^@]+@[^@]+$/', $email) !== 1) {
			return ['valid' => false, 'reason' => 'Email must contain one @ symbol with text on both sides.'];
		}

		$parts = explode('@', $email, 2);
		$username = $parts[0];
		$domain = $parts[1];

		if (preg_match('/^[A-Za-z0-9._-]+$/', $username) !== 1) {
			return ['valid' => false, 'reason' => 'Username can only use letters, numbers, dots, underscores, and hyphens.'];
		}

		if (strpos($domain, '.') === false) {
			return ['valid' => false, 'reason' => 'Domain must contain at least one dot (example.com).'];
		}

		$domainParts = explode('.', $domain);
		$tld = end($domainParts);

		if (preg_match('/^[A-Za-z]{2,63}$/', (string) $tld) !== 1) {
			return ['valid' => false, 'reason' => 'Top-level domain must be letters only and 2-63 characters long.'];
		}

		return ['valid' => false, 'reason' => 'Domain name has an invalid label format.'];
	}

	// Strict mode extras.
	if ($strictMode) {
		$parts = explode('@', $email, 2);
		$username = $parts[0];
		$domain = $parts[1];

		// Example strict rule from prompt: no consecutive dots in username.
		if (preg_match('/\.\./', $username) === 1) {
			return ['valid' => false, 'reason' => '(Strict mode) Username cannot contain consecutive dots.'];
		}

		// A couple of extra strict checks (common typo catchers).
		if (preg_match('/[._-]{2}/', $username) === 1) {
			return ['valid' => false, 'reason' => '(Strict mode) Username cannot have consecutive special characters.'];
		}

		if (strlen($username) > 64) {
			return ['valid' => false, 'reason' => '(Strict mode) Username must be 64 characters or less.'];
		}

		if (strlen($domain) > 253) {
			return ['valid' => false, 'reason' => '(Strict mode) Domain must be 253 characters or less.'];
		}
	}

	return ['valid' => true, 'reason' => 'Looks valid.'];
}

// Student note: this stores each checked email + result.
$results = [];
$strictMode = false;
$rawInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$rawInput = isset($_POST['emails']) ? (string) $_POST['emails'] : '';
	$strictMode = isset($_POST['strict_mode']);

	// One email per line.
	$lines = preg_split('/\r\n|\r|\n/', $rawInput);

	foreach ($lines as $line) {
		$email = trim($line);

		if ($email === '') {
			continue;
		}

		$results[] = [
			'email' => $email,
			'check' => validateEmail($email, $strictMode),
		];
	}
}

$validCount = 0;
$invalidCount = 0;

foreach ($results as $item) {
	if (!empty($item['check']['valid'])) {
		$validCount++;
	} else {
		$invalidCount++;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Email Validator</title>
	<style>
		* { box-sizing: border-box; }

		body {
			margin: 0;
			font-family: Verdana, Geneva, Tahoma, sans-serif;
			background: #eef2f7;
			color: #243447;
		}

		.wrapper {
			max-width: 900px;
			margin: 24px auto;
			padding: 0 14px;
		}

		.card {
			background: #ffffff;
			border: 1px solid #cfd8e3;
			border-radius: 10px;
			padding: 18px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
		}

		h1 {
			margin: 0 0 8px;
			font-size: 28px;
		}

		p.note {
			margin: 0 0 18px;
			color: #5b6b7c;
		}

		label {
			display: block;
			font-weight: 700;
			margin-bottom: 6px;
		}

		textarea {
			width: 100%;
			min-height: 170px;
			padding: 10px;
			border: 1px solid #cfd8e3;
			border-radius: 8px;
			font-family: "Courier New", monospace;
			font-size: 14px;
			resize: vertical;
		}

		.controls {
			display: flex;
			gap: 10px;
			align-items: center;
			margin: 12px 0 0;
			flex-wrap: wrap;
		}

		button {
			border: 1px solid #2d6cdf;
			background: #2d6cdf;
			color: #fff;
			font-weight: 700;
			padding: 9px 14px;
			border-radius: 8px;
			cursor: pointer;
		}

		button:hover {
			background: #1f57bb;
		}

		.summary {
			margin-top: 18px;
			padding-top: 14px;
			border-top: 1px solid #d8e0ea;
		}

		.result {
			padding: 10px;
			border-radius: 8px;
			margin-top: 10px;
			border: 1px solid transparent;
		}

		.result.valid {
			background: #e8f6ed;
			border-color: #b7e0c4;
		}

		.result.invalid {
			background: #fdeaea;
			border-color: #efc5c5;
		}

		.email {
			font-family: "Courier New", monospace;
			font-weight: 700;
			word-break: break-all;
		}

		.result.valid .email { color: #1f7a47; }
		.result.invalid .email { color: #9a2f2f; }

		.reason {
			margin-top: 4px;
			color: #556273;
			font-size: 14px;
		}

		.small {
			font-size: 13px;
			color: #5b6b7c;
			margin-top: 8px;
		}
	</style>
</head>
<body>
	<div class="wrapper">
		<div class="card">
			<h1>Email Validator</h1>
			<p class="note">Paste emails one per line. This checker uses regular expressions and optional strict mode.</p>

			<form method="post" novalidate>
				<label for="emails">Email addresses</label>
				<textarea id="emails" name="emails" placeholder="alice@example.com&#10;bob.smith@my-domain.org"><?= escape($rawInput) ?></textarea>

				<div class="controls">
					<input type="checkbox" id="strict_mode" name="strict_mode" <?= $strictMode ? 'checked' : '' ?>>
					<label for="strict_mode" style="margin:0;font-weight:600;">Strict mode</label>
					<button type="submit">Validate</button>
				</div>
				<div class="small">Strict mode adds extra rules like no consecutive dots in username.</div>
			</form>

			<?php if (!empty($results)): ?>
				<div class="summary">
					<strong>Total:</strong> <?= count($results) ?>
					| <span style="color:#1f7a47;"><strong>Valid:</strong> <?= $validCount ?></span>
					| <span style="color:#9a2f2f;"><strong>Invalid:</strong> <?= $invalidCount ?></span>
					<?= $strictMode ? '| <strong>Strict mode: ON</strong>' : '' ?>
				</div>

				<?php foreach ($results as $row): ?>
					<?php $ok = !empty($row['check']['valid']); ?>
					<div class="result <?= $ok ? 'valid' : 'invalid' ?>">
						<div class="email"><?= escape($row['email']) ?></div>
						<div class="reason"><?= escape($row['check']['reason']) ?></div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</body>
</html>
