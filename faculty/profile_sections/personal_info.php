<?php
require_once __DIR__ . '/../../db_connection.php';
// Get faculty ID from session
$faculty_id = $_SESSION['faculty_id'];

// Fetch faculty data
$query = "SELECT 
            f.*, 
            c.college_name, 
            p.*
          FROM faculty f 
          JOIN colleges c ON f.college_id = c.college_id 
          LEFT JOIN faculty_personal_info p ON f.faculty_id = p.faculty_id
          WHERE f.faculty_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$faculty_data = $result->fetch_assoc();
?>
<!-- Personal Information Section -->
<form id="personalInfoForm" onsubmit="submitPersonalInfo(event)">
    <div class="pds-section">
        <h2>I. PERSONAL INFORMATION</h2>
        <button type="button" class="edit-section-btn" onclick="toggleEdit('personal-info')">
            <i class="fas fa-edit"></i> Edit Section
        </button>
                
        <div id="personal-info">
            <table class="pds-table">
                <tr>
                    <th width="25%">Faculty ID</th>
                    <td class="readonly-field"><?php echo htmlspecialchars($faculty_data['faculty_id']); ?>
                        <i class="fas fa-lock" style="color:#1b6a0d; font-size:0.9em; float: right;"></i>
                    </td>

                    <th width="25%">Employment Type</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['employment_type']); ?></span>
                        <input type="text" name="employment_type" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['employment_type'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th>Full Name</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['full_name']); ?></span>
                        <input type="text" name="full_name" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['full_name'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>

                    <th>College</th>
                    <td class="readonly-field">
                        <?php echo htmlspecialchars($faculty_data['college_name']); ?>
                        <i class="fas fa-lock" style="color:#1b6a0d; font-size:0.9em; float: right;"></i>
                    </td>
                </tr>
                <tr>
                    <th>Date of Birth</th>
                    <td class="editable">
                        <span class="display-value"><?php echo !empty($faculty_data['birthdate']) ? date('F j, Y', strtotime($faculty_data['birthdate'])) : 'N/A'; ?></span>
                        <input type="date" name="birthdate" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['birthdate'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                    <th>Place of Birth</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['birthplace'] ?? 'N/A'); ?></span>
                        <input type="text" name="birthplace" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['birthplace'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['gender'] ?? 'N/A'); ?></span>
                        <select name="gender" class="edit-value" style="display: none;">
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo ($faculty_data['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($faculty_data['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                        </select>
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                    <th>Civil Status</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['civil_status'] ?? 'N/A'); ?></span>
                        <select name="civil_status" class="edit-value" style="display: none;">
                            <option value="">Select Status</option>
                            <option value="Single" <?php echo ($faculty_data['civil_status'] ?? '') == 'Single' ? 'selected' : ''; ?>>Single</option>
                            <option value="Married" <?php echo ($faculty_data['civil_status'] ?? '') == 'Married' ? 'selected' : ''; ?>>Married</option>
                            <option value="Widowed" <?php echo ($faculty_data['civil_status'] ?? '') == 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                            <option value="Separated" <?php echo ($faculty_data['civil_status'] ?? '') == 'Separated' ? 'selected' : ''; ?>>Separated</option>
                        </select>
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th>Height (cm)</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['height_cm'] ?? 'N/A'); ?></span>
                        <input type="number" step="0.01" name="height_cm" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['height_cm'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                    <th>Weight (kg)</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['weight_kg'] ?? 'N/A'); ?></span>
                        <input type="number" step="0.01" name="weight_kg" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['weight_kg'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th>Blood Type</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['blood_type'] ?? 'N/A'); ?></span>
                        <select name="blood_type" class="edit-value" style="display: none;">
                            <option value="">Select Blood Type</option>
                            <option value="A" <?php echo ($faculty_data['blood_type'] ?? '') == 'A' ? 'selected' : ''; ?>>A</option>
                            <option value="B" <?php echo ($faculty_data['blood_type'] ?? '') == 'B' ? 'selected' : ''; ?>>B</option>
                            <option value="AB" <?php echo ($faculty_data['blood_type'] ?? '') == 'AB' ? 'selected' : ''; ?>>AB</option>
                            <option value="O" <?php echo ($faculty_data['blood_type'] ?? '') == 'O' ? 'selected' : ''; ?>>O</option>
                            <option value="Unknown" <?php echo ($faculty_data['blood_type'] ?? '') == 'Unknown' ? 'selected' : ''; ?>>Unknown</option>
                        </select>
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                    <th>Citizenship</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['citizenship'] ?? 'N/A'); ?></span>
                        <input type="text" name="citizenship" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['citizenship'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th>Residential Address</th>
                    <td colspan="3" class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['address'] ?? 'N/A'); ?></span>
                        <textarea name="address" class="edit-value" style="display: none;"><?php echo htmlspecialchars($faculty_data['address'] ?? ''); ?></textarea>
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th>Contact Number</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['contact_number'] ?? 'N/A'); ?></span>
                        <input type="text" name="contact_number" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['contact_number'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                    <th>Email Address</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['email']); ?></span>
                        <input type="email" name="email" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['email'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th>Specialization</th>
                    <td colspan="3" class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['specialization'] ?? 'N/A'); ?></span>
                        <input type="text" name="specialization" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['specialization'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
            </table>
                    
            <!-- Government IDs -->
            <h3>Government Identification Numbers</h3>
            <table class="pds-table">
                <tr>
                    <th width="25%">GSIS ID No.</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['gsis_id_no'] ?? 'N/A'); ?></span>
                        <input type="text" name="gsis_id_no" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['gsis_id_no'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th width="25%">PAG-IBIG ID No.</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['pagibig_id_no'] ?? 'N/A'); ?></span>
                        <input type="text" name="pagibig_id_no" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['pagibig_id_no'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th>PhilHealth No.</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['philhealth_no'] ?? 'N/A'); ?></span>
                        <input type="text" name="philhealth_no" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['philhealth_no'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th>SSS No.</th>
                    <td class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['sss_no'] ?? 'N/A'); ?></span>
                        <input type="text" name="sss_no" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['sss_no'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
                <tr>
                    <th>TIN No.</th>
                    <td colspan="3" class="editable">
                        <span class="display-value"><?php echo htmlspecialchars($faculty_data['tin_no'] ?? 'N/A'); ?></span>
                        <input type="text" name="tin_no" class="edit-value" value="<?php echo htmlspecialchars($faculty_data['tin_no'] ?? ''); ?>" style="display: none;">
                        <span class="edit-toggle" onclick="toggleField(this)"><i class="fas fa-edit"></i></span>
                    </td>
                </tr>
            </table>
                    
            <div class="form-actions" style="display: none;">
                <input type="hidden" name="update_personal" value="1">
                <button type="submit" class="save-btn">Save Changes</button>
                <button type="button" class="cancel-btn" onclick="cancelEdit('personal-info')">Cancel</button>
            </div>
        </div>
    </div>
</form>