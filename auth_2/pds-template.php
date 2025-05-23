<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PERSONAL DATA SHEET</title>
    <link rel="stylesheet" href="../css/themes.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 20px 25px;
            color: var(--pds-text);
            background-color: var(--bg-color);
        }
        
        .pds-container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: var(--pds-bg);
            color: var(--pds-text);
        }

        .pds-section h1 {
            margin: 5px 0;
            font-size: 24px;
            color: var(--pds-header-text);
            text-align: center;
        }

        .pds-section p {
            margin: 5px 0;
            font-size: 12px;
            color: var(--pds-header-text);
            text-align: center;
        }
        
        .pds-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: var(--pds-section-bg);
            color: var(--pds-section-text);
            padding: 8px 12px;
            font-size: 18px;
            border-left: 4px solid var(--primary-color);
            margin-bottom: 15px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid var(--pds-table-border);
            background-color: var(--pds-table-row-bg);
        }
        
        .data-table th {
            background-color: #808080 ;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            border: 1px solid black;
        }
        
        .data-table td {
            padding: 10px;
            border: 1px solid black;
            color: var(--pds-text);
        }
        
        .data-table tr:nth-child(even) {
            background-color: var(--pds-table-row-alt-bg);
        }
        
        .data-table tr:hover {
            background-color: var(--pds-table-row-hover);
        }
        
        /* Compact table for smaller data */
        .compact-table {
            font-size: 13px;
        }
        
        .compact-table th, 
        .compact-table td {
            padding: 8px 10px;
        }
        
        /* Signature section */
        .signature-area {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-line {
            width: 250px;
            border-top: 1px solid var(--pds-border);
            text-align: center;
            padding-top: 5px;
            color: var(--pds-text);
        }
        
        /* Utility classes */
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .highlight {
            background-color: var(--primary-light);
            color: var(--pds-text);
        }
    </style>
</head>
<body>
    <div class="pds-container">
        <!-- I. PERSONAL INFORMATION -->
        <div class="pds-section">
            <h1>PERSONAL DATA SHEET</h1>
            <p>PAMANTASAN NG LUNGSOD NG PASIG</p> <hr>
            <div class="section-title">I. PERSONAL INFORMATION</div>
            
            <div style="display: flex;">
                <div style="flex: 1;">
                    <table class="data-table">
                        <tr>
                            <th width="30%">Faculty ID</th>
                            <td class="highlight"><?php echo htmlspecialchars($faculty_data['faculty_id'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td class="highlight"><?php echo htmlspecialchars($faculty_data['full_name'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Date of Birth</th>
                            <td><?php echo !empty($faculty_data['birthdate']) ? date('F j, Y', strtotime($faculty_data['birthdate'])) : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <th>Place of Birth</th>
                            <td><?php echo htmlspecialchars($faculty_data['birthplace'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td><?php echo htmlspecialchars($faculty_data['gender'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Civil Status</th>
                            <td><?php echo htmlspecialchars($faculty_data['civil_status'] ?? 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <table class="data-table">
                <tr>
                    <th width="25%">Employment Type</th>
                    <td><?php echo htmlspecialchars($faculty_data['employment_type'] ?? 'N/A'); ?></td>
                    <th width="25%">College</th>
                    <td><?php echo htmlspecialchars($faculty_data['college_name'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Height</th>
                    <td><?php echo htmlspecialchars($faculty_data['height_cm'] ?? 'N/A'); ?> cm</td>
                    <th>Weight</th>
                    <td><?php echo htmlspecialchars($faculty_data['weight_kg'] ?? 'N/A'); ?> kg</td>
                </tr>
                <tr>
                    <th>Blood Type</th>
                    <td><?php echo htmlspecialchars($faculty_data['blood_type'] ?? 'N/A'); ?></td>
                    <th>Citizenship</th>
                    <td><?php echo htmlspecialchars($faculty_data['citizenship'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Residential Address</th>
                    <td colspan="3"><?php echo htmlspecialchars($faculty_data['address'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Contact Number</th>
                    <td><?php echo htmlspecialchars($faculty_data['contact_number'] ?? 'N/A'); ?></td>
                    <th>Email Address</th>
                    <td><?php echo htmlspecialchars($faculty_data['email'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Specialization</th>
                    <td colspan="3"><?php echo htmlspecialchars($faculty_data['specialization'] ?? 'N/A'); ?></td>
                </tr>
            </table>
            
            <div class="section-title" style="margin-top: 25px;">Government Identification Numbers</div>
            <table class="data-table compact-table">
                <tr>
                    <th width="25%">GSIS ID No.</th>
                    <td><?php echo htmlspecialchars($faculty_data['gsis_id_no'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>PAG-IBIG ID No.</th>
                    <td><?php echo htmlspecialchars($faculty_data['pagibig_id_no'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>PhilHealth No.</th>
                    <td><?php echo htmlspecialchars($faculty_data['philhealth_no'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>SSS No.</th>
                    <td><?php echo htmlspecialchars($faculty_data['sss_no'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>TIN No.</th>
                    <td><?php echo htmlspecialchars($faculty_data['tin_no'] ?? 'N/A'); ?></td>
                </tr>
            </table>
        </div>
        <hr>

        <!-- II. EDUCATIONAL BACKGROUND -->
        <div class="pds-section">
            <div class="section-title">II. EDUCATIONAL BACKGROUND</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="15%">Level</th>
                        <th>Institution Name</th>
                        <th width="25%">Degree/Course</th>
                        <th width="15%">Years</th>
                        <th width="20%">Honors/Awards</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($academic_data->num_rows > 0): ?>
                        <?php while ($row = $academic_data->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['level'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['institution_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['degree_course'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($row['start_year'] ?? ''); ?>
                                    <?php echo ($row['start_year'] && $row['end_year']) ? ' - ' : ''; ?>
                                    <?php echo htmlspecialchars($row['end_year'] ?? ''); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['honors'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No educational background recorded</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <hr>

        <!-- III. CIVIL SERVICE ELIGIBILITY -->
        <div class="pds-section">
            <div class="section-title">III. CIVIL SERVICE ELIGIBILITY</div>
            <table class="data-table compact-table">
                <thead>
                    <tr>
                        <th width="25%">Eligibility</th>
                        <th width="10%">Rating</th>
                        <th width="15%">Exam Date</th>
                        <th width="20%">Place of Exam</th>
                        <th width="15%">License No.</th>
                        <th width="15%">Date Released</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($civil_data->num_rows > 0): ?>
                        <?php while ($row = $civil_data->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['eligibility_type'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['rating'] ?? 'N/A'); ?></td>
                                <td><?php echo !empty($row['date_of_examination']) ? date('M j, Y', strtotime($row['date_of_examination'])) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($row['place_of_examination'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['license_number'] ?? 'N/A'); ?></td>
                                <td><?php echo !empty($row['license_validity']) ? date('M j, Y', strtotime($row['license_validity'])) : 'N/A'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No civil service eligibility recorded</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <hr>

        <!-- IV. WORK EXPERIENCE -->
        <div class="pds-section">
            <div class="section-title">IV. WORK EXPERIENCE</div>
            <table class="data-table compact-table">
                <thead>
                    <tr>
                        <th width="20%">Position</th>
                        <th width="20%">Department/Agency</th>
                        <th width="12%">Salary Grade</th>
                        <th width="12%">Monthly Salary</th>
                        <th width="12%">Status</th>
                        <th width="12%">Gov't Service</th>
                        <th width="12%">Dates</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($work_data->num_rows > 0): ?>
                        <?php while ($row = $work_data->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['position_title'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['department_or_agency'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['salary_grade_step'] ?? 'N/A'); ?></td>
                                <td class="text-right"><?php echo isset($row['monthly_salary']) ? '₱' . number_format($row['monthly_salary'], 2) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($row['appointment_status'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['is_government_service'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php echo !empty($row['date_from']) ? date('M j, Y', strtotime($row['date_from'])) : 'N/A'; ?>
                                    <?php echo ($row['date_from'] && $row['date_to']) ? ' - ' : ''; ?>
                                    <?php echo !empty($row['date_to']) ? date('M j, Y', strtotime($row['date_to'])) : 'Present'; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No work experience recorded</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <hr>

        <!-- V. TRAINING PROGRAMS -->
        <div class="pds-section">
            <div class="section-title">V. TRAINING PROGRAMS ATTENDED</div>
            <table class="data-table compact-table">
                <thead>
                    <tr>
                        <th width="30%">Training Title</th>
                        <th width="25%">Conducted By</th>
                        <th width="15%">Type</th>
                        <th width="10%">Hours</th>
                        <th width="20%">Dates</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($training_data->num_rows > 0): ?>
                        <?php while ($row = $training_data->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['training_title'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['conducted_by'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['learning_type'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['number_of_hours'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php echo !empty($row['date_from']) ? date('M j, Y', strtotime($row['date_from'])) : 'N/A'; ?>
                                    <?php echo ($row['date_from'] && $row['date_to']) ? ' - ' : ''; ?>
                                    <?php echo !empty($row['date_to']) ? date('M j, Y', strtotime($row['date_to'])) : 'N/A'; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No training programs recorded</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <hr>

        <!-- SIGNATURE SECTION -->
        <div class="signature-area">
            <div class="signature-line">
                <strong>Signature Over Printed Name</strong>
            </div>
            <div class="signature-line">
                <strong>Date: <?php echo date('F j, Y'); ?></strong>
            </div>
        </div>
    </div>
</body>
</html>