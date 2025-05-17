<?php
require_once __DIR__ . '/../../db_connection.php';
// Get faculty ID from session
$faculty_id = $_SESSION['faculty_id'];
// Fetch civil service eligibility
$civil_query = "SELECT * FROM civil_service_eligibility WHERE faculty_id = ?";
$stmt = $conn->prepare($civil_query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$civil_data = $stmt->get_result();
?>
<!-- Civil Service Eligibility Section -->
<div class="pds-section">
    <h2>III. CIVIL SERVICE ELIGIBILITY</h2>
    <button type="button" class="edit-section-btn" onclick="openCivilServiceModal()">
        <i class="fas fa-plus"></i> Add Eligibility
    </button>
    <table class="pds-table">
        <thead>
            <tr>
                <th>Career Service/RA 1080(Board/Bar)<br>
                    Under Special Laws/CES/CSEE
                </th>
                <th>Rating</th>
                <th>Date of Examination</th>
                <th>Place of Examination</th>
                <th>License Number</th>
                <th>Date of Release (License)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($civil_data->num_rows > 0): ?>
                <?php while ($row = $civil_data->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['eligibility_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['rating'] ?? 'N/A'); ?></td>
                        <td><?php echo !empty($row['date_of_examination']) ? date('F j, Y', strtotime($row['date_of_examination'])) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($row['place_of_examination'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['license_number'] ?? 'N/A'); ?></td>
                        <td><?php echo !empty($row['license_validity']) ? date('F j, Y', strtotime($row['license_validity'])) : 'N/A'; ?></td>
                        <td>
                            <a title="Edit" onclick="editCivilService(<?php echo $row['id']; ?>)">
                                <i class="fas fa-edit " style="color:#1b6a0d; font-size:0.9em;"></i>
                            </a>
                            <a title="Delete" onclick="confirmDelete('service', <?php echo $row['id']; ?>, 'Delete this civil service record?')">
                                <i class="fas fa-trash-alt" style="color: #e74c3c; margin-left: 10px;"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No civil service eligibility recorded</td>
                    </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Civil Service Eligibility Modal -->
<div id="civilServiceModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('civilServiceModal')">&times;</span>
        <h2>Civil Service Eligibility</h2>
        <form id="civilServiceForm" method="POST" action="profile_api/save_service.php">
            <input type="hidden" name="id" id="civil_id">
            <input type="hidden" name="faculty_id" value="<?php echo $faculty_id; ?>">
                
            <div class="form-group">
                <label for="eligibility_type">Career Service/RA 1080(Board/Bar)<br>
                    Under Special Laws/CES/CSEE</label>
                <input type="text" name="eligibility_type" id="eligibility_type" required>
            </div>
                
            <div class="form-group">
                <label for="rating">Rating (if applicable)</label>
                <input type="text" name="rating" id="rating">
            </div>
                
            <div class="form-group">
                <label for="date_of_examination">Date of Examination *</label>
                <input type="date" name="date_of_examination" id="date_of_examination" required>
            </div>
                
            <div class="form-group">
                <label for="place_of_examination">Place of Examination *</label>
                <input type="text" name="place_of_examination" id="place_of_examination" required>
            </div>
                
            <div class="form-group">
                <label for="license_number">License Number (if applicable)</label>
                <input type="text" name="license_number" id="license_number">
            </div>
                
            <div class="form-group">
                <label for="license_validity">License Validity (if applicable)</label>
                <input type="date" name="license_validity" id="license_validity">
            </div>
                
            <div class="form-actions">
                <button type="submit" class="save-btn">Save</button>
                <button type="button" class="cancel-btn" onclick="closeModal('civilServiceModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>