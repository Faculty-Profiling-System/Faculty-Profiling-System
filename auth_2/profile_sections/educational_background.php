<?php
require_once __DIR__ . '/../../db_connection.php';
// Get faculty ID from session
$faculty_id = $_SESSION['faculty_id'];

// Fetch academic background
$academic_query = "SELECT * FROM academic_background WHERE faculty_id = ? ORDER BY end_year DESC";
$stmt = $conn->prepare($academic_query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$academic_data = $stmt->get_result();
?>
<!-- Educational Background Section -->
<div class="pds-section">
    <h2>II. EDUCATIONAL BACKGROUND</h2>
    <button type="button" class="edit-section-btn" onclick="openEducationModal()">
        <i class="fas fa-plus"></i> Add Education
    </button>
    <table class="pds-table">
        <thead>
            <tr>
                <th>Level</th>
                <th>Institution Name</th>
                <th>Degree Course</th>
                <th>Years Attended</th>
                <th>Honors</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($academic_data->num_rows > 0): ?>
                <?php while ($row = $academic_data->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['level']); ?></td>
                        <td><?php echo htmlspecialchars($row['institution_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['degree_course'] ?? 'N/A'); ?></td>
                        <td>
                            <?php echo htmlspecialchars($row['start_year'] ?? ''); ?>
                            <?php echo ($row['start_year'] && $row['end_year']) ? ' - ' : ''; ?>
                            <?php echo htmlspecialchars($row['end_year'] ?? ''); ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['honors'] ?? 'N/A'); ?></td>
                        <td>
                            <a title="Edit" onclick="editEducation(<?php echo $row['id']; ?>)">
                                <i class="fas fa-edit " style="color:#1b6a0d; font-size:0.9em;"></i>
                            </a>
                            <a title="Delete" onclick="confirmDelete('education', <?php echo $row['id']; ?>)">
                                <i class="fas fa-trash-alt" style="color: #e74c3c; margin-left: 10px;"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No educational background recorded</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Educational Background Modal -->
<div id="educationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('educationModal')">&times;</span>
        <h2>Educational Background</h2>
        <form id="educationForm" method="POST" action="profile_api/save_education.php">
            <input type="hidden" name="id" id="education_id">
            <input type="hidden" name="faculty_id" value="<?php echo $faculty_id; ?>">
                
            <div class="form-group">
                <label for="level">Level *</label>
                <select name="level" id="level" required>
                    <option value="">Select Educational Level</option>
                    <option value="Elementary">Elementary</option>
                    <option value="Secondary">Secondary</option>
                    <option value="Vocational / Trade Course">Vocational / Trade Course</option>
                    <option value="College">College</option>
                    <option value="Graduate Studies">Graduate Studies</option>
                </select>
            </div>
                
            <div class="form-group">
                <label for="institution_name">Institution Name *</label>
                <input type="text" name="institution_name" id="institution_name" required>
            </div>
                
            <div class="form-group">
                <label for="degree_course">Degree Course (if applicable)</label>
                <input type="text" name="degree_course" id="degree_course">
            </div>
                
            <div class="form-row">
                <div class="form-group">
                    <label for="start_year">Start Year *</label>
                    <input type="number" name="start_year" id="start_year" min="1900" max="<?php echo date('Y'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="end_year">End Year *</label>
                    <input type="number" name="end_year" id="end_year" min="1900" max="<?php echo date('Y'); ?>" required>
                </div>
            </div>
                
            <div class="form-group">
                <label for="honors">Honors/Awards (if any)</label>
                <input type="text" name="honors" id="honors">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="save-btn">Save</button>
                <button type="button" class="cancel-btn" onclick="closeModal('educationModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>