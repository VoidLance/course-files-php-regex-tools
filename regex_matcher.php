<?php
// Student note: this helper keeps output safe when printing user input.
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$sampleText = '';
$regexInput = '';
$modifier = '';
$errorMessage = '';
$matchCount = 0;
$matchesFound = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sampleText = isset($_POST['sample_text']) ? (string) $_POST['sample_text'] : '';
    $regexInput = isset($_POST['regex_pattern']) ? trim((string) $_POST['regex_pattern']) : '';
    $modifier = isset($_POST['modifier']) ? (string) $_POST['modifier'] : '';

    if ($regexInput === '') {
        $errorMessage = 'Please enter a regular expression pattern.';
    } else {
        // Student note: we treat input as pattern body and wrap it in /.../.
        $escapedPattern = str_replace('/', '\\/', $regexInput);
        $fullPattern = '/' . $escapedPattern . '/' . $modifier;

        // @ hides PHP warning if regex is invalid, then we show our own message.
        $result = @preg_match_all($fullPattern, $sampleText, $allMatches, PREG_OFFSET_CAPTURE);

        if ($result === false) {
            $errorMessage = 'Invalid regular expression. Double-check brackets, slashes, and special characters.';
        } else {
            $matchCount = $result;

            // Use only full matches from index 0.
            foreach ($allMatches[0] as $m) {
                $matchesFound[] = [
                    'text' => $m[0],
                    'position' => $m[1],
                ];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regex Matcher</title>
    <style>
        body {
            margin: 0;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            background: #eef2f7;
            color: #243447;
        }

        .wrap {
            max-width: 980px;
            margin: 24px auto;
            padding: 0 14px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #cfd8e3;
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 16px;
        }

        h1, h2 {
            margin: 0 0 10px;
        }

        p {
            margin: 0 0 12px;
            color: #556273;
        }

        label {
            display: block;
            font-weight: 700;
            margin: 12px 0 6px;
        }

        textarea,
        input,
        select,
        button {
            width: 100%;
            box-sizing: border-box;
            border-radius: 8px;
            font: inherit;
        }

        textarea,
        input,
        select {
            border: 1px solid #cfd8e3;
            padding: 10px;
            background: #fff;
            color: #243447;
        }

        textarea {
            min-height: 170px;
            resize: vertical;
            font-family: "Courier New", monospace;
        }

        button {
            margin-top: 14px;
            border: 1px solid #2d6cdf;
            background: #2d6cdf;
            color: #fff;
            font-weight: 700;
            padding: 10px;
            cursor: pointer;
        }

        button:hover {
            background: #1f57bb;
        }

        .error {
            background: #fdeaea;
            color: #9a2f2f;
            border: 1px solid #efc5c5;
            border-radius: 8px;
            padding: 10px;
            margin-top: 12px;
        }

        .result-summary {
            background: #e8f6ed;
            color: #1f7a47;
            border: 1px solid #b7e0c4;
            border-radius: 8px;
            padding: 10px;
            margin-top: 12px;
        }

        .match-item {
            border: 1px solid #d8e0ea;
            border-radius: 8px;
            padding: 10px;
            margin-top: 8px;
            background: #fafcff;
        }

        .match-text {
            font-family: "Courier New", monospace;
            font-weight: 700;
            word-break: break-all;
        }

        .small {
            font-size: 13px;
            color: #5b6b7c;
        }

        ul {
            margin: 8px 0 0 20px;
            padding: 0;
        }

        li {
            margin-bottom: 7px;
        }

        code {
            font-family: "Courier New", monospace;
            background: #f2f5f9;
            padding: 1px 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Regex Matcher</h1>
            <p>Type sample text, enter a regex pattern, pick a modifier, and this page will show all matches + positions.</p>

            <form method="post" novalidate>
                <label for="sample_text">Sample Text</label>
                <textarea id="sample_text" name="sample_text" placeholder="Put your test text here..."><?= e($sampleText) ?></textarea>

                <label for="regex_pattern">Regular Expression Pattern</label>
                <input id="regex_pattern" name="regex_pattern" type="text" placeholder="Example: cat|dog or \b\d+\b" value="<?= e($regexInput) ?>">
                <div class="small">Tip: enter only the pattern body. The app adds slashes automatically.</div>

                <label for="modifier">Modifier</label>
                <select id="modifier" name="modifier">
                    <option value="" <?= $modifier === '' ? 'selected' : '' ?>>None</option>
                    <option value="i" <?= $modifier === 'i' ? 'selected' : '' ?>>i (case-insensitive)</option>
                    <option value="m" <?= $modifier === 'm' ? 'selected' : '' ?>>m (multiline)</option>
                    <option value="s" <?= $modifier === 's' ? 'selected' : '' ?>>s (dot matches new lines)</option>
                    <option value="u" <?= $modifier === 'u' ? 'selected' : '' ?>>u (UTF-8 mode)</option>
                </select>

                <button type="submit">Find Matches</button>
            </form>

            <?php if ($errorMessage !== ''): ?>
                <div class="error"><?= e($errorMessage) ?></div>
            <?php endif; ?>

            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $errorMessage === ''): ?>
                <div class="result-summary">
                    <strong>Matches found:</strong> <?= $matchCount ?>
                </div>

                <?php if ($matchCount > 0): ?>
                    <?php foreach ($matchesFound as $index => $match): ?>
                        <div class="match-item">
                            <div><strong>Match #<?= $index + 1 ?></strong></div>
                            <div class="match-text"><?= e($match['text']) ?></div>
                            <div class="small">Position in original text: <?= e($match['position']) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="small" style="margin-top:10px;">No matches found for this pattern in the sample text.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Quick Regex Help (Student Cheat Sheet)</h2>
            <p>Some common patterns you will probably use a lot:</p>
            <ul>
                <li><code>\d+</code> = one or more digits</li>
                <li><code>[A-Za-z]+</code> = one or more letters</li>
                <li><code>\bword\b</code> = match whole word only</li>
                <li><code>^start</code> = line starts with "start"</li>
                <li><code>end$</code> = line ends with "end"</li>
                <li><code>cat|dog</code> = match "cat" or "dog"</li>
                <li><code>colou?r</code> = "color" and "colour"</li>
                <li><code>\s+</code> = one or more spaces/tabs/newlines</li>
            </ul>

            <p style="margin-top:14px;">Modifiers in this page:</p>
            <ul>
                <li><code>i</code> = case-insensitive (A and a are treated the same)</li>
                <li><code>m</code> = multiline mode (^ and $ work per line)</li>
                <li><code>s</code> = dotall mode (dot can match new lines)</li>
                <li><code>u</code> = UTF-8 mode (good for non-English text)</li>
            </ul>
        </div>
    </div>
</body>
</html>
