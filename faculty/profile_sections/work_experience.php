<?php
require_once __DIR__ . '/../../db_connection.php';
// Get faculty ID from session
$faculty_id = $_SESSION['faculty_id'];

// Fetch work experience
$work_query = "SELECT * FROM work_experience WHERE faculty_id = ? ORDER BY date_from DESC";
$stmt = $conn->prepare($work_query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$work_data = $stmt->get_result();
?>
<!-- Work Experience Section -->
<div class="pds-section">
    <h2>IV. WORK EXPERIENCE</h2>
    <button type="button" class="edit-section-btn" onclick="openWorkExperienceModal()">
        <i class="fas fa-plus"></i> Add Experience
    </button>
    <table class="pds-table">
        <thead>
            <tr>
                <th>Position Title</th>
                <th>Department/Agency</th>
                <th>Salary Grade/Step</th>
                <th>Monthly Salary</th>
                <th>Appointment Status</th>
                <th>Government Service</th>
                <th>Inclusive Dates</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($work_data->num_rows > 0): ?>
                <?php while ($row = $work_data->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['position_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['department_or_agency']); ?></td>
                        <td><?php echo htmlspecialchars($row['salary_grade_step'] ?? 'N/A'); ?></td>
                        <td><?php echo $row['monthly_salary'] ? '₱' . number_format($row['monthly_salary'], 2) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($row['appointment_status'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['is_government_service']); ?></td>
                        <td>
                            <?php echo !empty($row['date_from']) ? date('F j, Y', strtotime($row['date_from'])) : 'N/A'; ?>
                            <?php echo ($row['date_from'] && $row['date_to']) ? ' - ' : ''; ?>
                            <?php echo !empty($row['date_to']) ? date('F j, Y', strtotime($row['date_to'])) : 'Present'; ?>
                        </td>
                        <td>
                            <a title="Edit" onclick="editWorkExperience(<?php echo $row['id']; ?>)">
                                <i class="fas fa-edit " style="color:#1b6a0d; font-size:0.9em;"></i>
                            </a>
                            <a title="Delete" onclick="confirmDelete('experience', <?php echo $row['id']; ?>)">
                                <i class="fas fa-trash-alt" style="color: #e74c3c; margin-left: 10px;"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No work experience recorded</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Work Experience Modal -->
<div id="workExperienceModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('workExperienceModal')">&times;</span>
        <h2>Work Experience</h2>
        <form id="workExperienceForm" method="POST" action="profile_api/save_experience.php">
            <input type="hidden" name="id" id="work_id">
            <input type="hidden" name="faculty_id" value="<?php echo $faculty_id; ?>">
                
            <div class="form-group">
                <label for="position_title">Position Title *</label>
                <input type="text" name="position_title" id="position_title" required>
            </div>
                
            <div class="form-group">
                <label for="department_or_agency">Department/Agency *</label>
                <input type="text" name="department_or_agency" id="department_or_agency" required>
            </div>
                
            <div class="form-row">
                <div class="form-group">
                    <label for="salary_grade_step">Salary Grade/Step</label>
                    <input type="text" name="salary_grade_step" id="salary_grade_step">
                </div>
                
                <div class="form-group">
                    <label for="monthly_salary">Monthly Salary</label>
                    <input type="number" step="0.01" name="monthly_salary" id="monthly_salary">
                </div>
            </div>
                
            <div class="form-group">
                <label for="appointment_status">Appointment Status</label>
                <input type="text" name="appointment_status" id="appointment_status">
            </div>
                
            <div class="form-group">
                <label for="is_government_service">Government Service? *</label>
                <select name="is_government_service" id="is_government_service" required>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
                
            <div class="form-row">
                <div class="form-group">
                    <label for="date_from">From *</label>
                    <input type="date" name="date_from" id="date_from" required>
                </div>
                    
                <div class="form-group">
                    <label for="date_to">To (leave empty if present)</label>
                    <input type="date" name="date_to" id="date_to">
                </div>
            </div>
                
            <div class="form-actions">
                <button type="submit" class="save-btn">Save</button>
                <button type="button" class="cancel-btn" onclick="closeModal('workExperienceModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>