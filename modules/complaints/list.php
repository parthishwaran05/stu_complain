<?php
$pageTitle = 'My Complaints';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_login();
require_role(['student']);
?>

<div style="max-width: 1000px; margin: 0 auto;">
    <div class="card" style="border: 1px solid var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(12px);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;">My Activity</h2>
                <p>Track the status of your submitted concerns.</p>
            </div>
            <a href="create.php" class="btn primary">+ New Complaint</a>
        </div>

        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; padding: 1rem; background: var(--bg-soft); border-radius: var(--radius);">
            <div class="form-group">
                <label style="font-size: 0.75rem;">Status</label>
                <select id="filter-status">
                    <option value="">All Statuses</option>
                    <option value="Submitted">Submitted</option>
                    <option value="Under Review">Under Review</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Resolved">Resolved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
            <div class="form-group">
                <label style="font-size: 0.75rem;">Category</label>
                <select id="filter-category">
                    <option value="">All Categories</option>
                    <option value="Academics">Academics</option>
                    <option value="Hostel">Hostel</option>
                    <option value="Transport">Transport</option>
                    <option value="Infrastructure">Infrastructure</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="form-group">
                <label style="font-size: 0.75rem;">From Date</label>
                <input type="date" id="filter-from">
            </div>
            <div class="form-group">
                <label style="font-size: 0.75rem;">To Date</label>
                <input type="date" id="filter-to">
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-spacing: 0 8px;">
                <thead>
                    <tr style="background: transparent;">
                        <th>ID</th>
                        <th>Issue Summary</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody id="complaints-body">
                    <!-- Progressively loaded via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="<?= e($BASE_URL); ?>/assets/js/student.js"></script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
