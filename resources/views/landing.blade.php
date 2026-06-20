<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pannai Loan —  Financial Management Software</title>
  <meta name="description" content="Streamline loan applications, EMI calculations, customer management, payments, and reporting with advanced role-based access and secure authentication."/>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#eef2ff',
              100: '#e0e7ff',
              200: '#c7d2fe',
              300: '#a5b4fc',
              400: '#818cf8',
              500: '#6366f1',
              600: '#4f46e5',
              700: '#4338ca',
              800: '#3730a3',
              900: '#312e81'
            },
            accent: {
              500: '#06b6d4',
              600: '#0891b2'
            }
          },
          boxShadow: {
            glass: '0 10px 30px rgba(0,0,0,0.15)'
          }
        }
      },
      darkMode: 'class'
    }
  </script>
  <script src="https://d3js.org/d3.v7.min.js"></script>
  <style>
    :root {
      --grad-1: radial-gradient(1200px 600px at 10% -20%, rgba(99,102,241,0.35), transparent 60%),
                radial-gradient(900px 500px at 110% 10%, rgba(6,182,212,0.35), transparent 60%),
                linear-gradient(180deg, #0b1020 0%, #0a0f1a 100%);
      --card-bg: rgba(255,255,255,0.08);
      --card-border: rgba(255,255,255,0.12);
    }
    .bg-radial {
      background-image: var(--grad-1);
    }
    .glass {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      backdrop-filter: blur(10px);
    }
    .btn {
      transition: transform .15s ease, box-shadow .2s ease, background .2s ease;
    }
    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 25px rgba(99,102,241,0.35);
    }
    .shine {
      position: relative;
      overflow: hidden;
    }
    .shine::after {
      content: '';
      position: absolute;
      top: -150%;
      left: -150%;
      width: 300%;
      height: 300%;
      background: conic-gradient(from 180deg at 50% 50%, rgba(255,255,255,0.12), transparent 30%, rgba(255,255,255,0.12) 60%, transparent 100%);
      transform: rotate(0deg);
      animation: spin 10s linear infinite;
      pointer-events: none;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    .blob {
      filter: blur(40px);
      opacity: 0.6;
      animation: float 12s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0px) translateX(0px) scale(1); }
      50% { transform: translateY(-20px) translateX(10px) scale(1.05); }
    }
    .switch {
      height: 28px;
      width: 52px;
      border-radius: 9999px;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.2);
      position: relative;
      transition: background .2s ease;
    }
    .switch-dot {
      height: 22px;
      width: 22px;
      background: white;
      border-radius: 9999px;
      position: absolute;
      top: 50%;
      transform: translate(4px, -50%);
      transition: transform .2s ease, background .2s ease;
    }
    .switch-on {
      background: #4f46e5;
    }
    .switch-on .switch-dot {
      transform: translate(26px, -50%);
      background: #e0e7ff;
    }
    .tag {
      background: rgba(99,102,241,0.15);
      border: 1px solid rgba(99,102,241,0.35);
      color: #c7d2fe;
    }
    .table th {
      position: sticky;
      top: 0;
      background: rgba(10,15,26,0.7);
      backdrop-filter: blur(6px);
    }
    .link-underline {
      background-image: linear-gradient(currentColor, currentColor);
      background-position: 0% 100%;
      background-repeat: no-repeat;
      background-size: 0% 2px;
      transition: background-size .2s ease;
    }
    .link-underline:hover {
      background-size: 100% 2px;
    }
    .code {
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
  </style>
</head>
<body class="bg-radial text-white min-h-screen antialiased">
  <div class="absolute inset-0 pointer-events-none">
    <div class="blob absolute -top-20 -left-10 w-72 h-72 rounded-full bg-primary-600/40"></div>
    <div class="blob absolute top-20 right-10 w-80 h-80 rounded-full bg-accent-500/40" style="animation-delay: -3s;"></div>
  </div>

  <header class="relative z-10">
    <nav class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center shadow-glass">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h10M5 5h14M5 19h14"/>
          </svg>
        </div>
        <span class="text-xl font-semibold">Pannai Loan</span>
        {{-- <span class="hidden md:inline-block ml-3 px-2 py-1 rounded-lg tag text-xs">Laravel 10</span> --}}
      </div>
      <div class="hidden md:flex items-center gap-8">
        <a href="#features" class="text-white/80 hover:text-white link-underline">Features</a>
        <a href="#demo" class="text-white/80 hover:text-white link-underline">Live Demo</a>
        <a href="#reporting" class="text-white/80 hover:text-white link-underline">Reporting</a>
        <a href="#faq" class="text-white/80 hover:text-white link-underline">FAQ</a>
      </div>
      <div class="flex items-center gap-4">
        <div class="hidden sm:flex items-center gap-2">
          <span class="text-sm text-white/70">Dark</span>
          <button id="themeToggle" class="switch" aria-label="Toggle theme">
            <span class="switch-dot"></span>
          </button>
        </div>
      <a href="{{ url('login') }}" class="btn px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-500 shadow-glass">Login</a>
      </div>
    </nav>
  </header>

  <main class="relative z-10">
    <section class="max-w-7xl mx-auto px-6 pt-6 md:pt-12">
      <div class="grid md:grid-cols-2 gap-10 items-center">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass text-sm mb-5">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            Secure. Role-based. Real-time analytics.
          </div>
          <h1 class="text-4xl md:text-6xl font-bold leading-tight">
            Loan Financial Management Software
           
          </h1>
          <p class="mt-5 text-white/80 text-lg">
            Streamline loan applications, EMI calculations, customer lifecycle, and financial reporting with robust admin controls and secure authentication.
          </p>
          <div class="mt-8 flex flex-col sm:flex-row gap-4">
            <button id="ctaStart" class="btn px-6 py-3 rounded-xl bg-primary-600 hover:bg-primary-500 shadow-glass">Start Free Trial</button>
            <a href="#demo" class="btn px-6 py-3 rounded-xl glass hover:bg-white/10">Try Live Demo</a>
          </div>
          <div class="mt-6 flex items-center gap-4 text-sm text-white/70">
            <div class="flex -space-x-2">
              <img class="w-8 h-8 rounded-full border border-white/20" src="https://i.pravatar.cc/40?img=1" alt="user"/>
              <img class="w-8 h-8 rounded-full border border-white/20" src="https://i.pravatar.cc/40?img=2" alt="user"/>
              <img class="w-8 h-8 rounded-full border border-white/20" src="https://i.pravatar.cc/40?img=3" alt="user"/>
            </div>
            <span>Trusted by 500+ financial teams</span>
          </div>
        </div>
        <div class="relative">
          <div class="shine rounded-2xl p-1 glass">
            <div class="rounded-2xl bg-gradient-to-br from-primary-700/40 to-accent-600/40 p-6">
              <div class="grid grid-cols-2 gap-4">
                <div class="glass rounded-xl p-4">
                  <div class="text-sm text-white/70">Total Loans</div>
                  <div class="text-3xl font-semibold mt-1">128</div>
                  <div class="text-emerald-400 text-xs mt-2">+12% this month</div>
                </div>
                <div class="glass rounded-xl p-4">
                  <div class="text-sm text-white/70">Avg EMI</div>
                  <div class="text-3xl font-semibold mt-1">₹12,480</div>
                  <div class="text-emerald-400 text-xs mt-2">+3% this month</div>
                </div>
                <div class="glass rounded-xl p-4 col-span-2">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-sm text-white/70">On-time Payments</div>
                      <div class="text-2xl font-semibold mt-1">94.2%</div>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-lg tag">Finance KPI</span>
                  </div>
                  <div id="miniChart" class="mt-4 h-20"></div>
                </div>
              </div>
              <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                  <span class="text-sm">All systems operational</span>
                </div>
                <div class="text-sm text-white/70">Realtime Dashboard</div>
              </div>
            </div>
          </div>
          <div class="absolute -bottom-6 -left-6 w-24 h-24 rounded-2xl bg-primary-600/20 blur-xl"></div>
          <div class="absolute -top-6 -right-6 w-24 h-24 rounded-2xl bg-accent-500/20 blur-xl"></div>
        </div>
      </div>
    </section>

    <section id="features" class="max-w-7xl mx-auto px-6 mt-16 md:mt-24">
      <div class="grid md:grid-cols-3 gap-6">
        <div class="glass rounded-2xl p-6">
          <div class="w-10 h-10 rounded-lg bg-primary-600/30 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.567 3-3.5S13.657 4 12 4 9 5.567 9 7.5 10.343 11 12 11z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold">Role & Permission Management</h3>
          <p class="text-white/80 mt-2">Granular controls for Admin, Manager, Staff, and Accountant with intuitive ACLs.</p>
        </div>
        <div class="glass rounded-2xl p-6">
          <div class="w-10 h-10 rounded-lg bg-primary-600/30 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold">Loan Application Processing</h3>
          <p class="text-white/80 mt-2">End-to-end workflows from intake to approval with automated document checks.</p>
        </div>
        <div class="glass rounded-2xl p-6">
          <div class="w-10 h-10 rounded-lg bg-primary-600/30 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 11V7a4 4 0 118 0v4M3 21v-3M7 17h14M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold">Secure Authentication</h3>
          <p class="text-white/80 mt-2">Role-based access control, session security, and compliance-ready workflows.</p>
        </div>
      </div>
    </section>

    <section id="demo" class="max-w-7xl mx-auto px-6 mt-16 md:mt-24">
      <div class="grid lg:grid-cols-2 gap-8">
        <div class="glass rounded-2xl p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">EMI Calculator</h3>
            <span class="px-2 py-1 text-xs rounded-lg tag">Finance</span>
          </div>
          <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm text-white/70">Loan Amount (₹)</label>
              <input id="emiAmount" type="number" value="500000" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary-500"/>
            </div>
            <div>
              <label class="text-sm text-white/70">Interest Rate (%)</label>
              <input id="emiRate" type="number" value="8.5" step="0.1" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary-500"/>
            </div>
            <div>
              <label class="text-sm text-white/70">Tenure (months)</label>
              <input id="emiTenure" type="number" value="60" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary-500"/>
            </div>
            <div>
              <label class="text-sm text-white/70">Purpose</label>
              <select id="emiPurpose" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option>Home</option>
                <option>Auto</option>
                <option>Education</option>
                <option>Business</option>
              </select>
            </div>
          </div>
          <div class="mt-4 flex flex-wrap gap-3">
            <button id="calcEMI" class="btn px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-500">Calculate EMI</button>
            <button id="saveEMI" class="btn px-4 py-2 rounded-xl glass hover:bg-white/10">Save Scenario</button>
          </div>
          <div class="mt-5 grid grid-cols-3 gap-4">
            <div class="glass rounded-xl p-4 text-center">
              <div class="text-sm text-white/70">EMI</div>
              <div id="emiValue" class="text-2xl font-semibold mt-1">—</div>
            </div>
            <div class="glass rounded-xl p-4 text-center">
              <div class="text-sm text-white/70">Principal</div>
              <div id="emiPrincipal" class="text-2xl font-semibold mt-1">—</div>
            </div>
            <div class="glass rounded-xl p-4 text-center">
              <div class="text-sm text-white/70">Interest</div>
              <div id="emiInterest" class="text-2xl font-semibold mt-1">—</div>
            </div>
          </div>
          <div class="mt-5">
            <h4 class="text-sm text-white/70 mb-2">Saved Scenarios</h4>
            <div id="emiScenarios" class="flex flex-wrap gap-2"></div>
          </div>
        </div>

        <div class="glass rounded-2xl p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">Loan Application Processor</h3>
            <span class="px-2 py-1 text-xs rounded-lg tag"> Workflow</span>
          </div>
          <form id="loanForm" class="mt-4 grid grid-cols-1 gap-4">
            <div>
              <label class="text-sm text-white/70">Customer Name</label>
              <input required id="custName" type="text" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="John Doe"/>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-sm text-white/70">Amount (₹)</label>
                <input required id="loanAmount" type="number" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20" placeholder="500000"/>
              </div>
              <div>
                <label class="text-sm text-white/70">Tenure (m)</label>
                <input required id="loanTenure" type="number" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20" placeholder="60"/>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-sm text-white/70">Interest Rate (%)</label>
                <input required id="loanRate" type="number" step="0.1" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20" placeholder="8.5"/>
              </div>
              <div>
                <label class="text-sm text-white/70">Purpose</label>
                <select id="loanPurpose" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20">
                  <option>Home</option>
                  <option>Auto</option>
                  <option>Education</option>
                  <option>Business</option>
                </select>
              </div>
            </div>
            <div>
              <label class="text-sm text-white/70">Security Code</label>
              <input required id="securityCode" type="text" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20" placeholder="Enter code"/>
            </div>
            <div class="flex items-center gap-3">
              <button class="btn px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-500" type="submit">Submit Application</button>
              <button id="resetLoan" class="btn px-4 py-2 rounded-xl glass hover:bg-white/10" type="button">Reset</button>
            </div>
          </form>
          <div id="loanStatus" class="mt-4 text-sm text-white/80"></div>
        </div>
      </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 mt-16 md:mt-24">
      <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 glass rounded-2xl p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">Customer Management</h3>
            <div class="flex gap-2">
              <input id="customerSearch" placeholder="Search customers..." class="px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm"/>
              <button id="addCustomer" class="btn px-3 py-2 rounded-lg glass hover:bg-white/10 text-sm">Add</button>
            </div>
          </div>
          <div class="mt-4 overflow-x-auto">
            <table class="table w-full text-left text-sm">
              <thead>
                <tr class="text-white/70">
                  <th class="px-3 py-3">Name</th>
                  <th class="px-3 py-3">ID</th>
                  <th class="px-3 py-3">Loan</th>
                  <th class="px-3 py-3">Status</th>
                  <th class="px-3 py-3">Actions</th>
                </tr>
              </thead>
              <tbody id="customerTable"></tbody>
            </table>
          </div>
        </div>
        <div class="glass rounded-2xl p-6">
          <h3 class="text-xl font-semibold">Payment Tracking</h3>
          <div class="mt-4 space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-white/70">Total Outstanding</span>
              <span class="font-semibold" id="outstanding">₹0</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-white/70">On-time</span>
              <span class="font-semibold text-emerald-400" id="onTime">0%</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-white/70">Late</span>
              <span class="font-semibold text-amber-400" id="late">0%</span>
            </div>
          </div>
          <div class="mt-5">
            <h4 class="text-sm text-white/70 mb-2">Payments by Month</h4>
            <div id="paymentsChart" class="h-32"></div>
          </div>
        </div>
      </div>
    </section>

    <section id="reporting" class="max-w-7xl mx-auto px-6 mt-16 md:mt-24">
      <div class="grid md:grid-cols-3 gap-6">
        <div class="glass rounded-2xl p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Financial Reporting</h3>
            <span class="px-2 py-1 text-xs rounded-lg tag">Admin</span>
          </div>
          <div class="mt-4">
            <div class="text-sm text-white/70">Total Revenue</div>
            <div class="text-3xl font-semibold mt-1" id="revTotal">₹0</div>
            <div class="text-emerald-400 text-xs mt-2" id="revDelta">+0% vs last month</div>
          </div>
        </div>
        <div class="glass rounded-2xl p-6">
          <div class="text-sm text-white/70">Loan Approval Rate</div>
          <div class="text-3xl font-semibold mt-1" id="approvalRate">0%</div>
          <div class="text-white/80 text-sm mt-2">Average 92% last 30 days</div>
        </div>
        <div class="glass rounded-2xl p-6">
          <div class="text-sm text-white/70">Avg Loan Size</div>
          <div class="text-3xl font-semibold mt-1" id="avgLoan">—</div>
          <div class="text-white/80 text-sm mt-2">Filtered by tenure</div>
        </div>
      </div>
      <div class="glass rounded-2xl p-6 mt-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <h3 class="text-xl font-semibold">Admin Panel Analytics</h3>
          <div class="flex flex-wrap items-center gap-3">
            <select id="reportFilter" class="px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm">
              <option value="7">Last 7 days</option>
              <option value="30" selected>Last 30 days</option>
              <option value="90">Last 90 days</option>
            </select>
            <button id="exportCSV" class="btn px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-500 text-sm">Export CSV</button>
          </div>
        </div>
        <div class="grid md:grid-cols-2 gap-6 mt-6">
          <div>
            <h4 class="text-sm text-white/70 mb-2">Loans by Purpose</h4>
            <div id="purposeChart" class="h-64"></div>
          </div>
          <div>
            <h4 class="text-sm text-white/70 mb-2">Revenue Trend</h4>
            <div id="trendChart" class="h-64"></div>
          </div>
        </div>
      </div>
    </section>

    <section id="faq" class="max-w-7xl mx-auto px-6 mt-16 md:mt-24 mb-20">
      <div class="grid md:grid-cols-2 gap-6">
        <div class="glass rounded-2xl p-6">
          <h3 class="text-xl font-semibold">FAQs</h3>
          <div class="mt-4 space-y-3">
            <details class="glass rounded-xl p-4">
              <summary class="cursor-pointer font-medium">How do role permissions work?</summary>
              <p class="text-white/80 mt-2 text-sm">Admins configure permissions per role (Admin, Manager, Staff, Accountant). Each role can access specific modules and actions.</p>
            </details>
            <details class="glass rounded-xl p-4">
              <summary class="cursor-pointer font-medium">Is authentication secure?</summary>
              <p class="text-white/80 mt-2 text-sm">Yes. The demo uses secure localStorage to persist sessions. Production would integrate Laravel Sanctum with encrypted sessions and SSO.</p>
            </details>
            <details class="glass rounded-xl p-4">
              <summary class="cursor-pointer font-medium">Can I export reports?</summary>
              <p class="text-white/80 mt-2 text-sm">Use the Export CSV button to generate a CSV file of the dashboard analytics.</p>
            </details>
          </div>
        </div>
        <div class="glass rounded-2xl p-6">
          <h3 class="text-xl font-semibold">Security & Compliance</h3>
          <ul class="mt-4 space-y-2 text-white/80">
            <li class="flex items-start gap-3">
              <span class="mt-1 w-2 h-2 rounded-full bg-emerald-400"></span>
              Role-based access control (RBAC) with scoped permissions
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-2 h-2 rounded-full bg-emerald-400"></span>
              Data encryption at rest and in transit (production)
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-2 h-2 rounded-full bg-emerald-400"></span>
              Audit logs for key actions: loan approvals, payment changes, role edits
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-2 h-2 rounded-full bg-emerald-400"></span>
              HSTS, CSRF protection, and secure cookie policies
            </li>
          </ul>
        </div>
      </div>
    </section>
  </main>

  <footer class="relative z-10 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="text-white/70 text-sm">© <span id="year"></span> Pannai Loan. All rights reserved.</div>
      <div class="flex items-center gap-4 text-sm">
        <a href="#" class="text-white/70 hover:text-white link-underline">Privacy</a>
        <a href="#" class="text-white/70 hover:text-white link-underline">Terms</a>
        <a href="#" class="text-white/70 hover:text-white link-underline">Contact</a>
      </div>
    </div>
  </footer>

  <div id="authModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-6 z-50">
    <div class="glass rounded-2xl w-full max-w-md p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold">Sign In</h3>
        <button id="closeAuth" class="px-2 py-1 rounded-lg hover:bg-white/10">✕</button>
      </div>
      <form id="authForm" class="mt-4 space-y-4">
        <div>
          <label class="text-sm text-white/70">Email</label>
          <input required type="email" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20" placeholder="you@company.com"/>
        </div>
        <div>
          <label class="text-sm text-white/70">Password</label>
          <input required type="password" class="w-full mt-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20" placeholder="••••••••"/>
        </div>
        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 text-sm text-white/80">
            <input type="checkbox" class="accent-primary-500"/>
            Remember me
          </label>
          <a href="#" class="text-sm link-underline">Forgot?</a>
        </div>
        <button class="btn w-full px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-500">Sign In</button>
      </form>
      <div class="mt-4 text-center text-sm text-white/70">
        Demo only. Credentials not stored.
      </div>
    </div>
  </div>

  <script>
    const $ = (s) => document.querySelector(s);
    const $$ = (s) => document.querySelectorAll(s);

    const state = {
      role: localStorage.getItem('lf_role') || 'Guest',
      customers: JSON.parse(localStorage.getItem('lf_customers') || '[]'),
      loans: JSON.parse(localStorage.getItem('lf_loans') || '[]'),
      emiScenarios: JSON.parse(localStorage.getItem('lf_emis') || '[]'),
      payments: JSON.parse(localStorage.getItem('lf_payments') || '[]'),
      theme: localStorage.getItem('lf_theme') || 'dark',
      onetime: { ontime: 0.94, late: 0.06 }
    };

    function saveState() {
      localStorage.setItem('lf_customers', JSON.stringify(state.customers));
      localStorage.setItem('lf_loans', JSON.stringify(state.loans));
      localStorage.setItem('lf_emis', JSON.stringify(state.emiScenarios));
      localStorage.setItem('lf_payments', JSON.stringify(state.payments));
      localStorage.setItem('lf_theme', state.theme);
    }

    function formatMoney(n) {
      return '₹' + n.toLocaleString(undefined, { maximumFractionDigits: 0 });
    }

    function applyTheme() {
      if (state.theme === 'dark') {
        document.documentElement.classList.add('dark');
        $('#themeToggle').classList.add('switch-on');
      } else {
        document.documentElement.classList.remove('dark');
        $('#themeToggle').classList.remove('switch-on');
      }
    }

    function seedData() {
      if (state.customers.length === 0) {
        state.customers = [
          { id: 'CUS-1001', name: 'John Doe', loan: 500000, status: 'Active' },
          { id: 'CUS-1002', name: 'Priya Sharma', loan: 350000, status: 'Paid' },
          { id: 'CUS-1003', name: 'Rahul Mehta', loan: 800000, status: 'Active' },
        ];
      }
      if (state.loans.length === 0) {
        const purposes = ['Home', 'Auto', 'Education', 'Business'];
        for (let i = 0; i < 20; i++) {
          state.loans.push({
            id: 'LN-' + (1000 + i),
            amount: Math.floor(200000 + Math.random() * 1000000),
            rate: (4 + Math.random() * 10).toFixed(1),
            tenure: Math.floor(12 + Math.random() * 84),
            purpose: purposes[Math.floor(Math.random() * purposes.length)],
            status: ['Approved', 'Pending', 'Rejected'][Math.floor(Math.random() * 3)]
          });
        }
      }
      if (state.payments.length === 0) {
        const months = Array.from({ length: 12 }, (_, i) => new Date(new Date().getFullYear(), new Date().getMonth() - (11 - i), 1));
        months.forEach((d, i) => {
          state.payments.push({
            date: d.toISOString().slice(0,10),
            amount: Math.floor(20000 + Math.random() * 80000)
          });
        });
      }
      saveState();
    }

    function renderCustomers() {
      const tbody = $('#customerTable');
      const q = ($('#customerSearch').value || '').toLowerCase();
      tbody.innerHTML = '';
      state.customers
        .filter(c => c.name.toLowerCase().includes(q) || c.id.toLowerCase().includes(q))
        .forEach(c => {
          const tr = document.createElement('tr');
          tr.className = 'border-b border-white/10';
          tr.innerHTML = `
            <td class="px-3 py-3">${c.name}</td>
            <td class="px-3 py-3">${c.id}</td>
            <td class="px-3 py-3">${formatMoney(c.loan)}</td>
            <td class="px-3 py-3">
              <span class="px-2 py-1 text-xs rounded-lg ${c.status === 'Active' ? 'tag' : 'bg-white/10'}">${c.status}</span>
            </td>
            <td class="px-3 py-3">
              <button class="px-3 py-1 rounded-lg glass hover:bg-white/10 text-xs" data-action="edit" data-id="${c.id}">Edit</button>
              <button class="px-3 py-1 rounded-lg glass hover:bg-white/10 text-xs" data-action="del" data-id="${c.id}">Delete</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
    }

    function calcEMI(P, r, n) {
      r = r / 100 / 12;
      if (r === 0) return P / n;
      const E = P * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
      const total = E * n;
      const interest = total - P;
      return { E: Math.round(E), principal: P, interest: Math.round(interest) };
    }

    function renderEMI(scenarios) {
      const wrap = $('#emiScenarios');
      wrap.innerHTML = '';
      scenarios.forEach(s => {
        const b = document.createElement('button');
        b.className = 'px-3 py-2 rounded-lg glass hover:bg-white/10 text-xs';
        b.innerHTML = `${s.purpose} • ${formatMoney(s.amount)} @ ${s.rate}% / ${s.tenure}m`;
        b.addEventListener('click', () => {
          $('#emiAmount').value = s.amount;
          $('#emiRate').value = s.rate;
          $('#emiTenure').value = s.tenure;
          $('#emiPurpose').value = s.purpose;
          calculateEMI();
        });
        wrap.appendChild(b);
      });
    }

    function updateKPIs() {
      const total = state.payments.reduce((a, p) => a + p.amount, 0);
      $('#outstanding').textContent = formatMoney(total);
      const on = Math.round(state.onetime.ontime * 100);
      const lt = 100 - on;
      $('#onTime').textContent = on + '%';
      $('#late').textContent = lt + '%';
    }

    function renderCharts() {
      // Mini chart
      const miniWrap = d3.select('#miniChart');
      miniWrap.selectAll('*').remove();
      const w = miniWrap.node().clientWidth, h = miniWrap.node().clientHeight, m = {t:5,r:5,b:10,l:5};
      const svg = miniWrap.append('svg').attr('width', w).attr('height', h);
      const data = Array.from({length: 16}, (_, i) => ({ x: i, y: 60 + Math.sin(i/2)*15 + Math.random()*10 }));
      const x = d3.scaleLinear().domain([0, d3.max(data, d=>d.x)]).range([m.l, w - m.r]);
      const y = d3.scaleLinear().domain([0, 100]).range([h - m.b, m.t]);
      const line = d3.line().x(d=>x(d.x)).y(d=>y(d.y)).curve(d3.curveCatmullRom.alpha(0.5));
      svg.append('path').datum(data).attr('d', line).attr('fill', 'none').attr('stroke', '#60a5fa').attr('stroke-width', 2);
      svg.append('circle').attr('cx', x(data[data.length-1].x)).attr('cy', y(data[data.length-1].y)).attr('r', 3).attr('fill', '#60a5fa');

      // Payments bar chart
      const pwr = d3.select('#paymentsChart');
      pwr.selectAll('*').remove();
      const pw = pwr.node().clientWidth, ph = pwr.node().clientHeight;
      const svg2 = pwr.append('svg').attr('width', pw).attr('height', ph);
      const pData = state.payments.slice(-6);
      const y2 = d3.scaleBand().domain(pData.map(d=>d.date)).range([ph - 20, 0]).padding(0.2);
      const x2 = d3.scaleLinear().domain([0, d3.max(pData, d=>d.amount)]).range([0, pw - 10]);
      svg2.selectAll('rect').data(pData).enter().append('rect')
        .attr('x', 10).attr('y', d=>y2(d.date)).attr('width', d=>x2(d.amount)).attr('height', y2.bandwidth())
        .attr('fill', '#22d3ee');
      svg2.selectAll('text').data(pData).enter().append('text')
        .attr('x', 8).attr('y', d=>y2(d.date) - 6).attr('fill', 'white').attr('font-size', 10)
        .text(d=>d.date.slice(5));

      // Purpose donut
      const pw1 = d3.select('#purposeChart');
      pw1.selectAll('*').remove();
      const pwW = pw1.node().clientWidth, pwH = pw1.node().clientHeight;
      const svg3 = pw1.append('svg').attr('width', pwW).attr('height', pwH);
      const gp = svg3.append('g').attr('transform', `translate(${pwW/2},${pwH/2})`);
      const purposes = d3.rollups(state.loans, v=>v.length, d=>d.purpose).sort((a,b)=>b[1]-a[1]);
      const color = d3.scaleOrdinal().domain(purposes.map(d=>d[0])).range(['#818cf8','#22d3ee','#34d399','#f59e0b']);
      const radius = Math.min(pwW, pwH)/2 - 10;
      const arc = d3.arc().innerRadius(radius*0.6).outerRadius(radius);
      const pie = d3.pie().value(d=>d[1]);
      gp.selectAll('path').data(pie(purposes)).enter().append('path').attr('d', arc).attr('fill', d=>color(d.data[0])).attr('opacity', 0.9);
      gp.selectAll('text').data(pie(purposes)).enter().append('text')
        .attr('transform', d=>`translate(${arc.centroid(d)})`).attr('text-anchor', 'middle').attr('fill', 'white')
        .attr('font-size', 10).text(d=>d.data[0]);

      // Trend line
      const tr = d3.select('#trendChart');
      tr.selectAll('*').remove();
      const tw = tr.node().clientWidth, th = tr.node().clientHeight;
      const svg4 = tr.append('svg').attr('width', tw).attr('height', th);
      const trendData = d3.range(30).map(i => ({ x: i, y: 50000 + Math.sin(i/3)*12000 + Math.random()*10000 }));
      const xm = d3.scaleLinear().domain([0, d3.max(trendData, d=>d.x)]).range([40, tw-20]);
      const ym = d3.scaleLinear().domain([0, d3.max(trendData, d=>d.y)]).range([th-30, 20]);
      const line2 = d3.line().x(d=>xm(d.x)).y(d=>ym(d.y)).curve(d3.curveMonotoneX);
      svg4.append('path').datum(trendData).attr('d', line2).attr('fill', 'none').attr('stroke', '#a78bfa').attr('stroke-width', 2);
      svg4.selectAll('circle').data(trendData.slice(-6)).enter().append('circle')
        .attr('cx', d=>xm(d.x)).attr('cy', d=>ym(d.y)).attr('r', 3).attr('fill', '#a78bfa');
    }

    function updateReporting() {
      const rev = state.loans.reduce((a, l) => a + (l.amount * (l.rate/100/12) * l.tenure * 0.01 * 12), 0);
      $('#revTotal').textContent = formatMoney(Math.round(rev));
      const lastMonth = rev * (0.92 + Math.random()*0.1);
      $('#revDelta').textContent = `${Math.round((rev - lastMonth)/lastMonth*100)}% vs last month`;
      const approvals = state.loans.filter(l => l.status === 'Approved').length;
      $('#approvalRate').textContent = ((approvals / state.loans.length) * 100).toFixed(1) + '%';
      const avgLoan = Math.round(state.loans.reduce((a, l) => a + l.amount, 0) / state.loans.length);
      $('#avgLoan').textContent = formatMoney(avgLoan);
    }

    function calculateEMI() {
      const amount = parseFloat($('#emiAmount').value || 0);
      const rate = parseFloat($('#emiRate').value || 0);
      const tenure = parseInt($('#emiTenure').value || 0);
      const { E, principal, interest } = calcEMI(amount, rate, tenure);
      $('#emiValue').textContent = formatMoney(E);
      $('#emiPrincipal').textContent = formatMoney(principal);
      $('#emiInterest').textContent = formatMoney(interest);
    }

    function handleScenarioSave() {
      const scenario = {
        amount: parseFloat($('#emiAmount').value),
        rate: parseFloat($('#emiRate').value),
        tenure: parseInt($('#emiTenure').value),
        purpose: $('#emiPurpose').value
      };
      state.emiScenarios.push(scenario);
      state.emiScenarios = state.emiScenarios.slice(-8);
      saveState();
      renderEMI(state.emiScenarios);
    }

    function handleLoanSubmit(e) {
      e.preventDefault();
      const security = $('#securityCode').value.trim();
      if (security !== 'secure123') {
        $('#loanStatus').innerHTML = '<span class="text-amber-400">Security code invalid.</span>';
        return;
      }
      const loan = {
        id: 'LN-' + Math.floor(2000 + Math.random() * 8000),
        amount: parseFloat($('#loanAmount').value),
        rate: parseFloat($('#loanRate').value),
        tenure: parseInt($('#loanTenure').value),
        purpose: $('#loanPurpose').value,
        status: 'Approved'
      };
      state.loans.unshift(loan);
      saveState();
      $('#loanStatus').innerHTML = '<span class="text-emerald-400">Application submitted successfully.</span>';
      renderCharts();
      updateReporting();
      e.target.reset();
    }

    function handleCustomerActions(e) {
      const btn = e.target;
      if (!(btn?.dataset?.action)) return;
      const id = btn.dataset.id;
      if (btn.dataset.action === 'del') {
        state.customers = state.customers.filter(c => c.id !== id);
        saveState();
        renderCustomers();
      } else if (btn.dataset.action === 'edit') {
        const cust = state.customers.find(c => c.id === id);
        if (!cust) return;
        const newName = prompt('Update name', cust.name);
        if (newName !== null) {
          cust.name = newName;
          saveState();
          renderCustomers();
        }
      }
    }

    function exportCSV() {
      const headers = ['ID','Amount','Rate','Tenure','Purpose','Status'];
      const rows = [headers.join(',')].concat(state.loans.map(l => [l.id,l.amount,l.rate,l.tenure,l.purpose,l.status].map(v => `"${String(v).replace(/"/g,'""')}"`).join(',')));
      const blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'loanflow_report.csv';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    }

    function initTheme() {
      applyTheme();
      $('#themeToggle').addEventListener('click', () => {
        state.theme = state.theme === 'dark' ? 'light' : 'dark';
        applyTheme();
        saveState();
      });
    }

    function attachEvents() {
      $('#calcEMI').addEventListener('click', calculateEMI);
      $('#saveEMI').addEventListener('click', handleScenarioSave);
      $('#loanForm').addEventListener('submit', handleLoanSubmit);
      $('#resetLoan').addEventListener('click', (e) => { e.preventDefault(); e.target.form.reset(); $('#loanStatus').textContent=''; });
      $('#customerSearch').addEventListener('input', renderCustomers);
      $('#customerTable').addEventListener('click', handleCustomerActions);
      $('#reportFilter').addEventListener('change', renderCharts);
      $('#exportCSV').addEventListener('click', exportCSV);
      $('#closeAuth').addEventListener('click', () => { $('#authModal').classList.add('hidden'); });
      $('#authModal').addEventListener('click', (e) => { if (e.target === $('#authModal')) { $('#authModal').classList.add('hidden'); } });
    }

    function init() {
      document.getElementById('year').textContent = new Date().getFullYear();
      initTheme();
      seedData();
      renderCustomers();
      renderEMI(state.emiScenarios);
      calculateEMI();
      updateKPIs();
      updateReporting();
      renderCharts();
      attachEvents();
      // Responsive charts redraw
      window.addEventListener('resize', () => {
        renderCharts();
      });
    }

    document.addEventListener('DOMContentLoaded', init);
  </script>
</body>
</html>
