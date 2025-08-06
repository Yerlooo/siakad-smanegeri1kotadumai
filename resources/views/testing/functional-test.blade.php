<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAKAD - Pengujian Fungsional</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .test-nav {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .nav-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .nav-btn:hover,
        .nav-btn.active {
            background: rgba(255,255,255,0.3);
            border-color: white;
            transform: translateY(-2px);
        }
        
        .test-section {
            display: none;
        }
        
        .test-section.active {
            display: block;
        }
        
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .test-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .test-card:hover {
            transform: translateY(-5px);
        }
        
        .test-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .test-card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .test-form {
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
        .btn-danger { background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); }
        .btn-warning { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: #333; }
        .btn-info { background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); }
        
        .test-result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }
        
        .test-result.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .test-result.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .test-result.warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        
        .test-result.info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        
        .test-step {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #6c757d;
        }
        
        .test-step.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        
        .test-step.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        
        .test-step-icon {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .test-details {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .summary-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
        }
        
        .stat-item.passed {
            background: #d4edda;
            color: #155724;
        }
        
        .stat-item.failed {
            background: #f8d7da;
            color: #721c24;
        }
        
        .stat-item.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }
        
        .coverage-bar {
            width: 100%;
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .coverage-fill {
            height: 100%;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 10px;
            transition: width 0.3s ease;
            width: 0%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 SIAKAD Functional Testing</h1>
            <p>Pengujian White Box & Black Box untuk Sistem Informasi Akademik</p>
        </div>

        <div class="test-nav">
            <button class="nav-btn active" onclick="showSection('authentication')">🔐 Authentication</button>
            <button class="nav-btn" onclick="showSection('crud')">📝 CRUD Operations</button>
            <button class="nav-btn" onclick="showSection('validation')">✅ Data Validation</button>
            <button class="nav-btn" onclick="showSection('integration')">🔗 Integration</button>
            <button class="nav-btn" onclick="showSection('performance')">⚡ Performance</button>
            <button class="nav-btn" onclick="showSection('security')">🛡️ Security</button>
        </div>

        <!-- Authentication Testing Section -->
        <div id="authentication" class="test-section active">
            <div class="test-grid">
                <!-- Login Test -->
                <div class="test-card">
                    <h3>🔑 Login Function Test</h3>
                    <p>Menguji fungsionalitas login dengan berbagai skenario</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" id="login-email" value="admin@siakad.com" placeholder="Masukkan email">
                        </div>
                        <div class="form-group">
                            <label>Password:</label>
                            <input type="password" id="login-password" value="password123" placeholder="Masukkan password">
                        </div>
                    </div>
                    <button class="btn btn-success" onclick="testLogin('valid')">Test Login Valid</button>
                    <button class="btn btn-danger" onclick="testLogin('invalid')">Test Login Invalid</button>
                    <button class="btn btn-warning" onclick="testLogin('empty')">Test Empty Fields</button>
                    <div id="login-result" class="test-result"></div>
                    <div id="login-loading" class="loading">
                        <div class="spinner"></div>
                        <p>Menguji login...</p>
                    </div>
                </div>

                <!-- Session Management Test -->
                <div class="test-card">
                    <h3>⏱️ Session Management Test</h3>
                    <p>Menguji pengelolaan sesi pengguna dan timeout</p>
                    <button class="btn btn-info" onclick="testSessionStatus()">Check Session Status</button>
                    <button class="btn btn-warning" onclick="testSessionTimeout()">Test Session Timeout</button>
                    <button class="btn btn-danger" onclick="testLogout()">Test Logout</button>
                    <div id="session-result" class="test-result"></div>
                </div>

                <!-- Role-based Access Test -->
                <div class="test-card">
                    <h3>👥 Role-Based Access Test</h3>
                    <p>Menguji kontrol akses berdasarkan role pengguna</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Test Role:</label>
                            <select id="role-select">
                                <option value="kepala_tatausaha">Kepala Tata Usaha</option>
                                <option value="tata_usaha">Tata Usaha</option>
                                <option value="guru">Guru</option>
                                <option value="murid">Murid</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-info" onclick="testRoleAccess()">Test Access Control</button>
                    <button class="btn btn-warning" onclick="testUnauthorizedAccess()">Test Unauthorized Access</button>
                    <div id="role-result" class="test-result"></div>
                </div>
            </div>
        </div>

        <!-- CRUD Operations Testing Section -->
        <div id="crud" class="test-section">
            <div class="test-grid">
                <!-- Create Test -->
                <div class="test-card">
                    <h3>➕ Create Operation Test</h3>
                    <p>Menguji fungsi pembuatan data baru</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Entity Type:</label>
                            <select id="create-entity">
                                <option value="siswa">Siswa</option>
                                <option value="guru">Guru</option>
                                <option value="kelas">Kelas</option>
                                <option value="mata_pelajaran">Mata Pelajaran</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-success" onclick="testCreate('valid')">Test Valid Create</button>
                    <button class="btn btn-danger" onclick="testCreate('invalid')">Test Invalid Create</button>
                    <button class="btn btn-warning" onclick="testCreate('duplicate')">Test Duplicate Data</button>
                    <div id="create-result" class="test-result"></div>
                </div>

                <!-- Read Test -->
                <div class="test-card">
                    <h3>👁️ Read Operation Test</h3>
                    <p>Menguji fungsi pembacaan dan pencarian data</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Search Query:</label>
                            <input type="text" id="search-query" placeholder="Masukkan kata kunci pencarian">
                        </div>
                    </div>
                    <button class="btn btn-info" onclick="testRead('all')">Test Read All</button>
                    <button class="btn btn-info" onclick="testRead('search')">Test Search</button>
                    <button class="btn btn-warning" onclick="testRead('pagination')">Test Pagination</button>
                    <div id="read-result" class="test-result"></div>
                </div>

                <!-- Update Test -->
                <div class="test-card">
                    <h3>✏️ Update Operation Test</h3>
                    <p>Menguji fungsi pembaruan data yang sudah ada</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Record ID:</label>
                            <input type="number" id="update-id" value="1" placeholder="ID record yang akan diupdate">
                        </div>
                    </div>
                    <button class="btn btn-success" onclick="testUpdate('valid')">Test Valid Update</button>
                    <button class="btn btn-danger" onclick="testUpdate('nonexistent')">Test Update Non-existent</button>
                    <button class="btn btn-warning" onclick="testUpdate('partial')">Test Partial Update</button>
                    <div id="update-result" class="test-result"></div>
                </div>

                <!-- Delete Test -->
                <div class="test-card">
                    <h3>🗑️ Delete Operation Test</h3>
                    <p>Menguji fungsi penghapusan data</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Record ID:</label>
                            <input type="number" id="delete-id" value="999" placeholder="ID record yang akan dihapus">
                        </div>
                    </div>
                    <button class="btn btn-success" onclick="testDelete('valid')">Test Valid Delete</button>
                    <button class="btn btn-danger" onclick="testDelete('nonexistent')">Test Delete Non-existent</button>
                    <button class="btn btn-warning" onclick="testDelete('cascade')">Test Cascade Delete</button>
                    <div id="delete-result" class="test-result"></div>
                </div>
            </div>
        </div>

        <!-- Data Validation Testing Section -->
        <div id="validation" class="test-section">
            <div class="test-grid">
                <!-- Input Validation Test -->
                <div class="test-card">
                    <h3>🔍 Input Validation Test</h3>
                    <p>Menguji validasi input data dari pengguna</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Test Data Type:</label>
                            <select id="validation-type">
                                <option value="email">Email</option>
                                <option value="phone">Phone Number</option>
                                <option value="date">Date</option>
                                <option value="number">Number</option>
                                <option value="required">Required Fields</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Test Input:</label>
                            <input type="text" id="validation-input" placeholder="Masukkan data untuk divalidasi">
                        </div>
                    </div>
                    <button class="btn btn-success" onclick="testValidation('valid')">Test Valid Input</button>
                    <button class="btn btn-danger" onclick="testValidation('invalid')">Test Invalid Input</button>
                    <button class="btn btn-warning" onclick="testValidation('edge')">Test Edge Cases</button>
                    <div id="validation-result" class="test-result"></div>
                </div>

                <!-- Business Rules Test -->
                <div class="test-card">
                    <h3>📋 Business Rules Test</h3>
                    <p>Menguji aturan bisnis dan logika aplikasi</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Rule Type:</label>
                            <select id="business-rule">
                                <option value="enrollment">Student Enrollment Rules</option>
                                <option value="grading">Grading System Rules</option>
                                <option value="attendance">Attendance Rules</option>
                                <option value="schedule">Scheduling Rules</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-info" onclick="testBusinessRules()">Test Business Logic</button>
                    <button class="btn btn-warning" onclick="testRuleConstraints()">Test Rule Constraints</button>
                    <div id="business-result" class="test-result"></div>
                </div>

                <!-- Data Integrity Test -->
                <div class="test-card">
                    <h3>🔒 Data Integrity Test</h3>
                    <p>Menguji integritas dan konsistensi data</p>
                    <button class="btn btn-info" onclick="testDataIntegrity()">Test Data Consistency</button>
                    <button class="btn btn-warning" onclick="testForeignKeys()">Test Foreign Key Constraints</button>
                    <button class="btn btn-danger" onclick="testDataCorruption()">Test Data Corruption Handling</button>
                    <div id="integrity-result" class="test-result"></div>
                </div>
            </div>
        </div>

        <!-- Integration Testing Section -->
        <div id="integration" class="test-section">
            <div class="test-grid">
                <!-- API Integration Test -->
                <div class="test-card">
                    <h3>🌐 API Integration Test</h3>
                    <p>Menguji integrasi dengan layanan eksternal</p>
                    <button class="btn btn-info" onclick="testAPIIntegration()">Test External APIs</button>
                    <button class="btn btn-warning" onclick="testAPIErrorHandling()">Test API Error Handling</button>
                    <button class="btn btn-danger" onclick="testAPITimeout()">Test API Timeout</button>
                    <div id="api-result" class="test-result"></div>
                </div>

                <!-- Database Integration Test -->
                <div class="test-card">
                    <h3>🗄️ Database Integration Test</h3>
                    <p>Menguji koneksi dan operasi database</p>
                    <button class="btn btn-success" onclick="testDatabaseConnection()">Test DB Connection</button>
                    <button class="btn btn-info" onclick="testTransactions()">Test Transactions</button>
                    <button class="btn btn-warning" onclick="testConcurrency()">Test Concurrency</button>
                    <div id="db-result" class="test-result"></div>
                </div>

                <!-- Module Integration Test -->
                <div class="test-card">
                    <h3>🧩 Module Integration Test</h3>
                    <p>Menguji integrasi antar modul sistem</p>
                    <button class="btn btn-info" onclick="testModuleIntegration()">Test Module Communication</button>
                    <button class="btn btn-warning" onclick="testDataFlow()">Test Data Flow</button>
                    <div id="module-result" class="test-result"></div>
                </div>
            </div>
        </div>

        <!-- Performance Testing Section -->
        <div id="performance" class="test-section">
            <div class="test-grid">
                <!-- Load Test -->
                <div class="test-card">
                    <h3>⚡ Load Testing</h3>
                    <p>Menguji kinerja aplikasi di bawah beban normal</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Concurrent Users:</label>
                            <input type="number" id="load-users" value="10" min="1" max="100">
                        </div>
                        <div class="form-group">
                            <label>Test Duration (seconds):</label>
                            <input type="number" id="load-duration" value="30" min="5" max="300">
                        </div>
                    </div>
                    <button class="btn btn-info" onclick="testLoad()">Run Load Test</button>
                    <button class="btn btn-warning" onclick="testStress()">Run Stress Test</button>
                    <div id="load-result" class="test-result"></div>
                </div>

                <!-- Response Time Test -->
                <div class="test-card">
                    <h3>⏱️ Response Time Test</h3>
                    <p>Menguji waktu respon berbagai operasi</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Test Endpoint:</label>
                            <select id="endpoint-select">
                                <option value="login">Login Page</option>
                                <option value="dashboard">Dashboard</option>
                                <option value="siswa">Student List</option>
                                <option value="guru">Teacher List</option>
                                <option value="nilai">Grades</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-info" onclick="testResponseTime()">Test Response Time</button>
                    <button class="btn btn-warning" onclick="testThroughput()">Test Throughput</button>
                    <div id="response-result" class="test-result"></div>
                </div>

                <!-- Memory & Resource Test -->
                <div class="test-card">
                    <h3>💾 Resource Usage Test</h3>
                    <p>Menguji penggunaan memori dan resource sistem</p>
                    <button class="btn btn-info" onclick="testMemoryUsage()">Test Memory Usage</button>
                    <button class="btn btn-warning" onclick="testResourceLeaks()">Test Resource Leaks</button>
                    <button class="btn btn-danger" onclick="testResourceLimits()">Test Resource Limits</button>
                    <div id="resource-result" class="test-result"></div>
                </div>
            </div>
        </div>

        <!-- Security Testing Section -->
        <div id="security" class="test-section">
            <div class="test-grid">
                <!-- Authentication Security Test -->
                <div class="test-card">
                    <h3>🔐 Authentication Security Test</h3>
                    <p>Menguji keamanan sistem autentikasi</p>
                    <button class="btn btn-warning" onclick="testBruteForce()">Test Brute Force Protection</button>
                    <button class="btn btn-danger" onclick="testSessionHijacking()">Test Session Security</button>
                    <button class="btn btn-info" onclick="testPasswordSecurity()">Test Password Security</button>
                    <div id="auth-security-result" class="test-result"></div>
                </div>

                <!-- Input Security Test -->
                <div class="test-card">
                    <h3>💉 Input Security Test</h3>
                    <p>Menguji keamanan input dari berbagai serangan</p>
                    <div class="test-form">
                        <div class="form-group">
                            <label>Test Input:</label>
                            <textarea id="security-input" rows="3" placeholder="<script>alert('XSS')</script>"></textarea>
                        </div>
                    </div>
                    <button class="btn btn-danger" onclick="testXSS()">Test XSS Protection</button>
                    <button class="btn btn-danger" onclick="testSQLInjection()">Test SQL Injection</button>
                    <button class="btn btn-warning" onclick="testCSRF()">Test CSRF Protection</button>
                    <div id="input-security-result" class="test-result"></div>
                </div>

                <!-- Authorization Security Test -->
                <div class="test-card">
                    <h3>🛡️ Authorization Security Test</h3>
                    <p>Menguji kontrol akses dan otorisasi</p>
                    <button class="btn btn-warning" onclick="testPrivilegeEscalation()">Test Privilege Escalation</button>
                    <button class="btn btn-danger" onclick="testDirectObjectReference()">Test Direct Object Reference</button>
                    <button class="btn btn-info" onclick="testAccessControl()">Test Access Control</button>
                    <div id="authz-security-result" class="test-result"></div>
                </div>
            </div>
        </div>

        <!-- Test Summary -->
        <div class="summary-card">
            <h2>📊 Test Summary & Coverage</h2>
            <div class="summary-stats">
                <div class="stat-item passed">
                    <span class="stat-number" id="tests-passed">0</span>
                    <span>Tests Passed</span>
                </div>
                <div class="stat-item failed">
                    <span class="stat-number" id="tests-failed">0</span>
                    <span>Tests Failed</span>
                </div>
                <div class="stat-item pending">
                    <span class="stat-number" id="tests-pending">0</span>
                    <span>Tests Pending</span>
                </div>
            </div>
            <div>
                <h4>Test Coverage</h4>
                <div class="coverage-bar">
                    <div class="coverage-fill" id="coverage-bar"></div>
                </div>
                <p>Coverage: <span id="coverage-percent">0%</span></p>
            </div>
            <button class="btn btn-success" onclick="runAllTests()">🚀 Run All Tests</button>
            <button class="btn btn-warning" onclick="resetTests()">🔄 Reset Tests</button>
            <button class="btn btn-info" onclick="exportResults()">📄 Export Results</button>
        </div>
    </div>

    <script src="{{ asset('js/functional-test.js') }}"></script>
</body>
</html>
