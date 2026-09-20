<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Jev — Real-Time Semantic Decision Engine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #090d16;
            --bg-surface: #0f172a;
            --bg-surface-elevated: #1e293b;
            --border-subtle: #334155;
            --border-highlight: #475569;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-primary: #6366f1;
            --accent-primary-hover: #4f46e5;
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
            --accent-cyan: #06b6d4;
            --radius-md: 10px;
            --radius-lg: 16px;
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-base);
            color: var(--text-primary);
            line-height: 1.5;
            min-height: 100vh;
            padding: 24px;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 20px;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        }

        .brand h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .brand p {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 9999px;
            background-color: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.25);
            font-size: 12px;
            font-weight: 600;
        }

        .status-pill .dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        /* Nav Tabs */
        .tabs {
            display: flex;
            gap: 8px;
            background-color: var(--bg-surface);
            padding: 6px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-subtle);
            margin-bottom: 24px;
            overflow-x: auto;
        }

        .tab-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-btn:hover {
            color: var(--text-primary);
            background-color: rgba(255, 255, 255, 0.04);
        }

        .tab-btn.active {
            color: #ffffff;
            background-color: var(--accent-primary);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        /* Layout Grid */
        .workbench {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 24px;
        }

        @media (max-width: 900px) {
            .workbench {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 24px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .presets-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .presets-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }

        .preset-btn {
            background-color: var(--bg-surface-elevated);
            border: 1px solid var(--border-subtle);
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .preset-btn:hover {
            color: var(--text-primary);
            border-color: var(--accent-primary);
            background-color: rgba(99, 102, 241, 0.1);
        }

        .textarea-wrapper {
            position: relative;
            margin-bottom: 16px;
        }

        textarea {
            width: 100%;
            height: 120px;
            background-color: var(--bg-base);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 14px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text-primary);
            resize: vertical;
            outline: none;
            transition: border-color 0.15s ease;
        }

        textarea:focus {
            border-color: var(--accent-primary);
        }

        .action-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-run {
            background-color: var(--accent-primary);
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .btn-run:hover {
            background-color: var(--accent-primary-hover);
            transform: translateY(-1px);
        }

        .btn-run:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Results Display */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .metric-box {
            background-color: var(--bg-base);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 14px;
        }

        .metric-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .metric-val {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-danger {
            background-color: rgba(244, 63, 94, 0.15);
            color: #fb7185;
            border: 1px solid rgba(244, 63, 94, 0.3);
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-primary {
            background-color: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        /* Progress Bar */
        .progress-bar-bg {
            width: 100%;
            height: 8px;
            background-color: var(--bg-surface-elevated);
            border-radius: 9999px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 9999px;
            background: linear-gradient(90deg, #6366f1, #10b981);
            transition: width 0.4s ease;
        }

        /* Latency Badge */
        .latency-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background-color: rgba(6, 182, 212, 0.08);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: var(--radius-md);
            margin-bottom: 16px;
        }

        .latency-title {
            font-size: 12px;
            color: var(--accent-cyan);
            font-weight: 600;
        }

        .latency-time {
            font-family: var(--font-mono);
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
        }

        /* Code Sample Box */
        .code-block {
            background-color: #030712;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 14px;
            font-family: var(--font-mono);
            font-size: 12px;
            color: #cbd5e1;
            overflow-x: auto;
            position: relative;
        }

        .copy-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background-color: var(--bg-surface-elevated);
            color: var(--text-secondary);
            border: 1px solid var(--border-subtle);
            border-radius: 4px;
            font-size: 11px;
            padding: 4px 8px;
            cursor: pointer;
        }

        .copy-btn:hover {
            color: #ffffff;
            border-color: var(--accent-primary);
        }

        /* Custom Sandbox Controls */
        .sandbox-controls {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 10px 12px;
            background-color: var(--bg-base);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 13px;
            outline: none;
        }

        .form-input:focus, .form-select:focus {
            border-color: var(--accent-primary);
        }

        .slider-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        input[type="range"] {
            flex: 1;
            accent-color: var(--accent-primary);
        }

        .slider-val {
            font-family: var(--font-mono);
            font-size: 13px;
            font-weight: 700;
            color: var(--accent-primary);
            min-width: 44px;
        }

        /* Validation Form */
        .validation-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .error-msg {
            font-size: 12px;
            color: #fb7185;
            margin-top: 4px;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 48px 16px;
            color: var(--text-muted);
        }

        .empty-state-icon {
            font-size: 32px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="brand">
                <div class="brand-logo">J</div>
                <div>
                    <h1>Laravel Jev Operations Console</h1>
                    <p>Real-time classification and decision playground</p>
                </div>
            </div>
            <div>
                <div class="status-pill">
                    <span class="dot"></span>
                    <span>{{ config('jev.simulate', true) || empty(config('jev.api_key')) ? 'Simulation Mode' : 'Live API' }}</span>
                </div>
            </div>
        </header>

        <!-- Scenario Tabs -->
        <nav class="tabs" id="scenario-tabs">
            <button class="tab-btn active" data-tab="customer_chat">Support Triage</button>
            <button class="tab-btn" data-tab="sales_qualification">Lead Qualification</button>
            <button class="tab-btn" data-tab="review_moderation">Review Moderation</button>
            <button class="tab-btn" data-tab="batch_analysis">Batch Analysis</button>
            <button class="tab-btn" data-tab="custom_sandbox">Custom Sandbox</button>
            <button class="tab-btn" data-tab="form_validation">Form Validation</button>
        </nav>

        <!-- Main Workbench -->
        <div class="workbench">
            <!-- Left: Input & Presets -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title" id="input-title">Customer Inbound Message</div>
                </div>

                <div id="presets-container">
                    <div class="presets-label">Preset Scenarios</div>
                    <div class="presets-grid" id="presets-list">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- Custom Sandbox Dynamic Config (Hidden unless sandbox tab active) -->
                <div id="sandbox-config" style="display: none;">
                    <div class="sandbox-controls">
                        <div class="form-group">
                            <label>Decision Type</label>
                            <select id="sandbox-mode" class="form-select">
                                <option value="boolean">Jev::is() (Boolean Decision)</option>
                                <option value="choose">Jev::choose() (Categorical Routing)</option>
                                <option value="score">Jev::score() (Numerical Scale)</option>
                            </select>
                        </div>
                        <div class="form-group" id="sandbox-criteria-group">
                            <label id="sandbox-criteria-label">Criteria</label>
                            <input type="text" id="sandbox-criteria" class="form-input" value="spam or advertising">
                        </div>
                    </div>

                    <div class="form-group" id="sandbox-threshold-group" style="margin-bottom: 16px;">
                        <label>Confidence Threshold</label>
                        <div class="slider-wrapper">
                            <input type="range" id="sandbox-threshold" min="0.50" max="0.95" step="0.05" value="0.80">
                            <span class="slider-val" id="sandbox-threshold-val">80%</span>
                        </div>
                    </div>

                    <div class="form-group" id="sandbox-options-group" style="display: none; margin-bottom: 16px;">
                        <label>Options (Comma-separated)</label>
                        <input type="text" id="sandbox-options" class="form-input" value="billing, support, sales, security">
                    </div>
                </div>

                <!-- Regular Text Input -->
                <div id="text-input-section">
                    <div class="textarea-wrapper">
                        <textarea id="input-text" placeholder="Type or select a preset message to evaluate..."></textarea>
                    </div>

                    <div class="action-row">
                        <span style="font-size: 12px; color: var(--text-muted);" id="char-count">0 chars</span>
                        <button class="btn-run" id="btn-analyze">
                            <span>Evaluate</span>
                        </button>
                    </div>
                </div>

                <!-- Native Form Request Section (Only active on form_validation tab) -->
                <div id="form-validation-section" style="display: none;">
                    <form id="feedback-form" class="validation-form">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-input" value="Sarah Jenkins" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-input" value="sarah@example.com" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" class="form-select">
                                <option value="feature_request">Feature Request</option>
                                <option value="bug_report">Bug Report</option>
                                <option value="general_feedback">General Feedback</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Feedback Message</label>
                            <textarea name="message" id="form-message" style="height: 90px;" placeholder="Enter message..."></textarea>
                            <div class="error-msg" id="validation-error"></div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="submit" class="btn-run" id="btn-submit-form" style="flex: 1;">
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Decision Results & Code Inspector -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Decision Output</div>
                </div>

                <div class="latency-card">
                    <div>
                        <div class="latency-title">Execution Latency</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Round-trip execution duration</div>
                    </div>
                    <div class="latency-time" id="metric-latency">-- ms</div>
                </div>

                <div id="results-display">
                    <div class="empty-state">
                        <div style="font-size: 14px; font-weight: 600; color: var(--text-secondary);">Awaiting Evaluation</div>
                        <div style="font-size: 12px; margin-top: 4px;">Select a preset scenario on the left or enter text to analyze.</div>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <div class="presets-label">Executed Code</div>
                    <div class="code-block">
                        <button class="copy-btn" id="btn-copy-code">Copy</button>
                        <pre><code id="code-snippet">// Select a scenario or run an evaluation to view code.</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const state = {
            currentTab: 'customer_chat',
            presets: {},
            loading: false
        };

        const elements = {
            tabs: document.querySelectorAll('.tab-btn'),
            presetsList: document.getElementById('presets-list'),
            presetsContainer: document.getElementById('presets-container'),
            inputText: document.getElementById('input-text'),
            inputTitle: document.getElementById('input-title'),
            textInputSection: document.getElementById('text-input-section'),
            formValidationSection: document.getElementById('form-validation-section'),
            sandboxConfig: document.getElementById('sandbox-config'),
            sandboxMode: document.getElementById('sandbox-mode'),
            sandboxCriteria: document.getElementById('sandbox-criteria'),
            sandboxCriteriaLabel: document.getElementById('sandbox-criteria-label'),
            sandboxCriteriaGroup: document.getElementById('sandbox-criteria-group'),
            sandboxThreshold: document.getElementById('sandbox-threshold'),
            sandboxThresholdVal: document.getElementById('sandbox-threshold-val'),
            sandboxThresholdGroup: document.getElementById('sandbox-threshold-group'),
            sandboxOptions: document.getElementById('sandbox-options'),
            sandboxOptionsGroup: document.getElementById('sandbox-options-group'),
            btnAnalyze: document.getElementById('btn-analyze'),
            resultsDisplay: document.getElementById('results-display'),
            metricLatency: document.getElementById('metric-latency'),
            codeSnippet: document.getElementById('code-snippet'),
            btnCopyCode: document.getElementById('btn-copy-code'),
            charCount: document.getElementById('char-count'),
            feedbackForm: document.getElementById('feedback-form'),
            formMessage: document.getElementById('form-message'),
            validationError: document.getElementById('validation-error')
        };

        // Fetch presets on load
        async function loadPresets() {
            try {
                const res = await fetch('/api/triage/presets');
                const data = await res.json();
                state.presets = data;
                renderPresets();
            } catch (err) {
                console.error('Failed to load presets', err);
            }
        }

        function renderPresets() {
            elements.presetsList.innerHTML = '';
            const list = state.presets[state.currentTab] || [];
            
            if (list.length === 0) {
                elements.presetsContainer.style.display = 'none';
                return;
            }

            elements.presetsContainer.style.display = 'block';

            list.forEach(item => {
                const btn = document.createElement('button');
                btn.className = 'preset-btn';
                btn.textContent = `${item.title} [${item.tag}]`;
                btn.onclick = () => {
                    elements.inputText.value = item.message;
                    updateCharCount();
                    runAnalysis();
                };
                elements.presetsList.appendChild(btn);
            });

            // Set default text from first preset
            if (list.length > 0 && !elements.inputText.value) {
                elements.inputText.value = list[0].message;
                updateCharCount();
            }
        }

        function updateCharCount() {
            elements.charCount.textContent = `${elements.inputText.value.length} chars`;
        }

        elements.inputText.addEventListener('input', updateCharCount);

        // Tab Switching
        elements.tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                elements.tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                state.currentTab = tab.dataset.tab;

                // Adjust UI per tab
                if (state.currentTab === 'form_validation') {
                    elements.textInputSection.style.display = 'none';
                    elements.sandboxConfig.style.display = 'none';
                    elements.presetsContainer.style.display = 'none';
                    elements.formValidationSection.style.display = 'block';
                    elements.inputTitle.textContent = 'Form Request Validation';
                    elements.codeSnippet.textContent = `$request->validate([\n    'message' => [\n        'required', 'string',\n        JevRule::not('spam or abusive'),\n        JevRule::is('constructive product feedback'),\n    ],\n]);`;
                } else if (state.currentTab === 'custom_sandbox') {
                    elements.textInputSection.style.display = 'block';
                    elements.sandboxConfig.style.display = 'block';
                    elements.presetsContainer.style.display = 'none';
                    elements.formValidationSection.style.display = 'none';
                    elements.inputTitle.textContent = 'Custom Rule Sandbox';
                    elements.codeSnippet.textContent = `// Configure criteria, mode, and threshold above to test custom rules.`;
                } else {
                    elements.textInputSection.style.display = 'block';
                    elements.sandboxConfig.style.display = 'none';
                    elements.formValidationSection.style.display = 'none';
                    elements.inputTitle.textContent = tab.textContent;
                    elements.inputText.value = '';
                    renderPresets();
                }
            });
        });

        // Sandbox Controls
        elements.sandboxThreshold.addEventListener('input', (e) => {
            elements.sandboxThresholdVal.textContent = `${Math.round(e.target.value * 100)}%`;
        });

        elements.sandboxMode.addEventListener('change', (e) => {
            const mode = e.target.value;
            if (mode === 'boolean') {
                elements.sandboxCriteriaGroup.style.display = 'block';
                elements.sandboxCriteriaLabel.textContent = 'Criteria';
                elements.sandboxThresholdGroup.style.display = 'block';
                elements.sandboxOptionsGroup.style.display = 'none';
            } else if (mode === 'choose') {
                elements.sandboxCriteriaGroup.style.display = 'none';
                elements.sandboxThresholdGroup.style.display = 'none';
                elements.sandboxOptionsGroup.style.display = 'block';
            } else if (mode === 'score') {
                elements.sandboxCriteriaGroup.style.display = 'block';
                elements.sandboxCriteriaLabel.textContent = 'Criteria to Score';
                elements.sandboxThresholdGroup.style.display = 'none';
                elements.sandboxOptionsGroup.style.display = 'block';
            }
        });

        // Run Analysis
        elements.btnAnalyze.addEventListener('click', runAnalysis);

        async function runAnalysis() {
            const input = elements.inputText.value.trim();
            if (!input) return;

            elements.btnAnalyze.disabled = true;
            elements.btnAnalyze.textContent = 'Evaluating...';

            try {
                let url = '/api/triage/analyze';
                let payload = {
                    scenario: state.currentTab,
                    input: input
                };

                if (state.currentTab === 'custom_sandbox') {
                    url = '/api/triage/sandbox';
                    const mode = elements.sandboxMode.value;
                    let params = {};

                    if (mode === 'boolean') {
                        params = {
                            criteria: elements.sandboxCriteria.value,
                            threshold: parseFloat(elements.sandboxThreshold.value)
                        };
                    } else if (mode === 'choose') {
                        params = {
                            options: elements.sandboxOptions.value.split(',').map(s => s.trim())
                        };
                    } else if (mode === 'score') {
                        params = {
                            criteria: elements.sandboxCriteria.value,
                            levels: elements.sandboxOptions.value.split(',').map(s => s.trim())
                        };
                    }

                    payload = {
                        input: input,
                        mode: mode,
                        params: params
                    };
                }

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const json = await res.json();
                renderResults(json.data);
            } catch (err) {
                console.error(err);
            } finally {
                elements.btnAnalyze.disabled = false;
                elements.btnAnalyze.innerHTML = '<span>Evaluate</span>';
            }
        }

        function renderResults(data) {
            elements.metricLatency.textContent = `${data.duration_ms} ms`;
            elements.codeSnippet.textContent = data.code_sample || '// Execution finished';

            if (data.scenario === 'customer_chat') {
                elements.resultsDisplay.innerHTML = `
                    <div class="metric-grid">
                        <div class="metric-box">
                            <div class="metric-label">Spam Status</div>
                            <div class="metric-val">
                                ${data.is_spam 
                                    ? '<span class="badge badge-danger">Spam</span>' 
                                    : '<span class="badge badge-success">Clean</span>'}
                            </div>
                        </div>
                        <div class="metric-box">
                            <div class="metric-label">Routed Department</div>
                            <div class="metric-val">
                                <span class="badge badge-primary">${data.department}</span>
                            </div>
                        </div>
                        <div class="metric-box">
                            <div class="metric-label">Urgency Score</div>
                            <div class="metric-val">${Math.round(data.urgency_score * 100)}%</div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: ${data.urgency_score * 100}%"></div>
                            </div>
                        </div>
                        <div class="metric-box">
                            <div class="metric-label">Customer Frustration</div>
                            <div class="metric-val">${Math.round(data.frustration_score * 100)}%</div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: ${data.frustration_score * 100}%"></div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (data.scenario === 'sales_qualification') {
                elements.resultsDisplay.innerHTML = `
                    <div class="metric-grid">
                        <div class="metric-box">
                            <div class="metric-label">Enterprise Intent</div>
                            <div class="metric-val">
                                ${data.is_enterprise 
                                    ? '<span class="badge badge-success">Enterprise Intent</span>' 
                                    : '<span class="badge badge-primary">Standard Lead</span>'}
                            </div>
                        </div>
                        <div class="metric-box">
                            <div class="metric-label">Account Tier</div>
                            <div class="metric-val"><span class="badge badge-primary">${data.segment}</span></div>
                        </div>
                        <div class="metric-box" style="grid-column: span 2;">
                            <div class="metric-label">Deal Scale Potential</div>
                            <div class="metric-val">${Math.round(data.deal_scale_score * 100)}%</div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: ${data.deal_scale_score * 100}%"></div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (data.scenario === 'review_moderation') {
                elements.resultsDisplay.innerHTML = `
                    <div class="metric-grid">
                        <div class="metric-box">
                            <div class="metric-label">Moderation Status</div>
                            <div class="metric-val">
                                ${data.is_approved 
                                    ? '<span class="badge badge-success">Approved</span>' 
                                    : '<span class="badge badge-danger">Flagged</span>'}
                            </div>
                        </div>
                        <div class="metric-box">
                            <div class="metric-label">Topic</div>
                            <div class="metric-val"><span class="badge badge-primary">${data.topic}</span></div>
                        </div>
                        <div class="metric-box" style="grid-column: span 2;">
                            <div class="metric-label">Sentiment Score</div>
                            <div class="metric-val">${Math.round(data.sentiment_score * 100)}%</div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: ${data.sentiment_score * 100}%"></div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (data.scenario === 'batch_analysis') {
                elements.resultsDisplay.innerHTML = `
                    <div class="metric-grid">
                        <div class="metric-box">
                            <div class="metric-label">Actionable</div>
                            <div class="metric-val">
                                ${data.actionable 
                                    ? '<span class="badge badge-success">Actionable</span>' 
                                    : '<span class="badge badge-danger">Not Actionable</span>'}
                            </div>
                        </div>
                        <div class="metric-box">
                            <div class="metric-label">Assigned Team</div>
                            <div class="metric-val"><span class="badge badge-primary">${data.team}</span></div>
                        </div>
                        <div class="metric-box">
                            <div class="metric-label">Priority Level</div>
                            <div class="metric-val">${Math.round(data.urgency_level * 100)}%</div>
                        </div>
                        <div class="metric-box">
                            <div class="metric-label">Round-Trips</div>
                            <div class="metric-val" style="color: #34d399;">1 Request</div>
                        </div>
                    </div>
                `;
            } else if (data.scenario === 'custom_sandbox') {
                const res = data.result;
                let details = '';
                if (res.mode === 'boolean') {
                    details = `
                        <div class="metric-box" style="grid-column: span 2;">
                            <div class="metric-label">Match: "${res.criteria}" (${Math.round(res.threshold * 100)}% threshold)</div>
                            <div class="metric-val">
                                ${res.matches 
                                    ? '<span class="badge badge-success">True</span>' 
                                    : '<span class="badge badge-danger">False</span>'}
                            </div>
                        </div>
                    `;
                } else if (res.mode === 'choose') {
                    details = `
                        <div class="metric-box" style="grid-column: span 2;">
                            <div class="metric-label">Selected Option</div>
                            <div class="metric-val"><span class="badge badge-primary">${res.selected}</span></div>
                        </div>
                    `;
                } else if (res.mode === 'score') {
                    details = `
                        <div class="metric-box" style="grid-column: span 2;">
                            <div class="metric-label">Score: "${res.criteria}" [${res.levels.join(', ')}]</div>
                            <div class="metric-val">${Math.round(res.score * 100)}% (${res.score})</div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: ${res.score * 100}%"></div>
                            </div>
                        </div>
                    `;
                }

                elements.resultsDisplay.innerHTML = `<div class="metric-grid">${details}</div>`;
            }
        }

        // Form Validation Submission
        elements.feedbackForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            elements.validationError.textContent = '';
            const formData = new FormData(elements.feedbackForm);
            const payload = Object.fromEntries(formData.entries());

            try {
                const res = await fetch('/api/feedback/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!res.ok) {
                    const err = data.errors?.message?.[0] || 'Validation failed.';
                    elements.validationError.textContent = err;
                    elements.resultsDisplay.innerHTML = `
                        <div class="metric-box" style="border-color: rgba(244,63,94,0.4);">
                            <div class="metric-label" style="color: #fb7185;">Validation Rejected</div>
                            <div style="font-size: 13px; color: #f8fafc; margin-top: 6px;">${err}</div>
                        </div>
                    `;
                } else {
                    elements.resultsDisplay.innerHTML = `
                        <div class="metric-box" style="border-color: rgba(16,185,129,0.4);">
                            <div class="metric-label" style="color: #34d399;">Validation Passed</div>
                            <div style="font-size: 13px; color: #f8fafc; margin-top: 6px;">${data.message}</div>
                        </div>
                    `;
                }
            } catch (err) {
                console.error(err);
            }
        });

        // Copy Code Button
        elements.btnCopyCode.addEventListener('click', () => {
            navigator.clipboard.writeText(elements.codeSnippet.textContent);
            elements.btnCopyCode.textContent = 'Copied!';
            setTimeout(() => elements.btnCopyCode.textContent = 'Copy', 1500);
        });

        // Initial load
        loadPresets();
    </script>
</body>
</html>
