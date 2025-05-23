<?php
require_once __DIR__ . '/../../db_connection.php';
// Get faculty ID from session
$faculty_id = $_SESSION['faculty_id'];

// Fetch training programs
$training_query = "SELECT * FROM training_programs WHERE faculty_id = ? ORDER BY date_from DESC";
$stmt = $conn->prepare($training_query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$training_data = $stmt->get_result();
?>
<!-- Training Programs Section -->
<div class="pds-section">
    <h2>V. TRAINING PROGRAMS ATTENDED</h2>
    <button type="button" class="edit-section-btn" onclick="openTrainingModal()">
        <i class="fas fa-plus"></i> Add Training
    </button>
    <table class="pds-table">
        <thead>
            <tr>
                <th>Training Title</th>
                <th>Conducted By</th>
                <th>Learning Type</th>
                <th>Hours</th>
                <th>Inclusive Dates</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($training_data->num_rows > 0): ?>
                <?php while ($row = $training_data->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['training_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['conducted_by']); ?></td>
                        <td><?php echo htmlspecialchars($row['learning_type'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['number_of_hours'] ?? 'N/A'); ?></td>
                        <td>
                            <?php echo !empty($row['date_from']) ? date('F j, Y', strtotime($row['date_from'])) : 'N/A'; ?>
                            <?php echo ($row['date_from'] && $row['date_to']) ? ' - ' : ''; ?>
                            <?php echo !empty($row['date_to']) ? date('F j, Y', strtotime($row['date_to'])) : 'N/A'; ?>
                        </td>
                        <td>
                            <a title="Edit" onclick="editTraining(<?php echo $row['id']; ?>)">
                                <i class="fas fa-edit " style="color:#1b6a0d; font-size:0.9em;"></i>
                            </a>
                            <a title="Delete" onclick="confirmDelete('training', <?php echo $row['id']; ?>)">
                                <i class="fas fa-trash-alt" style="color: #e74c3c; margin-left: 10px;"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No training programs recorded</td>
                    </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Training Programs Modal -->
<div id="trainingModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('trainingModal')">&times;</span>
        <h2>Training Program</h2>
        <form id="trainingForm" method="POST" action="profile_api/save_training.php">
            <input type="hidden" name="id" id="training_id">
            <input type="hidden" name="faculty_id" value="<?php echo $faculty_id; ?>">
                
            <div class="form-group">
                <label for="training_title">Training Title *</label>
                <input type="text" name="training_title" id="training_title" required>
            </div>
                
            <div class="form-group">
                <label for="conducted_by">Conducted By *</label>
                <input type="text" name="conducted_by" id="conducted_by" required>
            </div>
                
            <div class="form-group">
                <label for="learning_type">Learning Type</label>
                <select name="learning_type" id="learning_type">
                    <option value="Management">Management</option>
                    <option value="Supervisory">Supervisory</option>
                    <option value="Technical">Technical</option>
                    <option value="Other">Other</option>
                </select>
            </div>
                
            <div class="form-group">
                <label for="number_of_hours">Number of Hours *</label>
                <input type="number" 
                    name="number_of_hours" 
                    id="number_of_hours" 
                    min="1" 
                    onkeydown="return event.keyCode !== 69 && event.keyCode !== 189" 
                    required>
            </div>
                
            <div class="form-row">
                <div class="form-group">
                    <label for="training_date_from">From *</label>
                    <input type="date" name="date_from" id="training_date_from" required>
                </div>
                    
                <div class="form-group">
                    <label for="training_date_to">To *</label>
                    <input type="date" name="date_to" id="training_date_to" required>
                </div>
            </div>
                
            <div class="form-actions">
                <button type="submit" class="save-btn">Save</button>
                <button type="button" class="cancel-btn" onclick="closeModal('trainingModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>