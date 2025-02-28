<!-- Sidebar -->
<aside id="sidebar">
    <div class="sidebar-title">
        <div class="sidebar-brand">
            <a href="index.php" style="text-decoration: none; color: #9e9ea4;">
                <span class="material-icons-outlined">admin_panel_settings</span> K8FCS ADMIN
            </a>
        </div>
        <span class="material-icons-outlined" onclick="closeSidebar()">close</span>
    </div>

    <ul class="sidebar-list">
        <li class="sidebar-list-item">
            <a class="title" href="index.php">
                <span class="material-icons-outlined">dashboard</span> Dashboard
            </a>
        </li>
        <li class="sidebar-list-item">
            <a class="title" onclick="toggleSubmenu(event, 'process-submenu')">
                <span class="material-icons-outlined">settings</span> Process Client
            </a>

            <ul class="submenu" id="process-submenu">
                <li class="submenu-item"><a href="pending-appointments.php">Pending Client</a></li>
                <li class="submenu-item"><a href="accepted-appointments.php">Approve Client</a></li>
                <li class="submenu-item"><a href="approve-payment.php">Check Payment</a></li>
                <li class="submenu-item"><a href="archives.php">Archived</a></li>

            </ul>

        </li>
        <li class="sidebar-list-item">
            <a class="title" onclick="toggleSubmenu(event, 'manage-submenu')">
                <span class="material-icons-outlined">manage_accounts</span> Management
            </a>
            <ul class="submenu" id="manage-submenu">
                <li class="submenu-item"><a href="manage-account.php">Create Account</a></li>
                <li class="submenu-item"><a href="edit-delete-account.php">Update Account</a></li>
                <li class="submenu-item"><a href="show-active-inactive.php">Active Status</a></li>
                <li class="submenu-item"><a href="client-details.php">Account Statement</a></li>
                <li class="submenu-item"><a href="all-client-files.php">File</a></li>

            </ul>
        </li>

        <li class="sidebar-list-item">
            <a class="title" onclick="toggleSubmenu(event, 'report-submenu')">
                <span class="material-icons-outlined">description</span> Reports
            </a>
            <ul class="submenu" id="report-submenu">
                <li class="submenu-item"><a href="report.php">Clients</a></li>
                <li class="submenu-item"><a href="sales.php">Sales</a></li>
                <li class="submenu-item"><a href="user-accounts.php">User Accounts</a></li>
                <li class="submenu-item"><a href="application-report.php">Application</a></li>
                <li class="submenu-item"><a href="booked-account-report.php">Booked Account</a></li>
                <li class="submenu-item"><a href="commission-report.php">Commission</a></li>
            </ul>
        </li>

        <li class="sidebar-list-item">
            <a class="title" href="analytics.php">
                <span class="material-icons-outlined">analytics</span> Analytics
            </a>
        </li>
        <li class="sidebar-list-item">
            <a class="title" href="update-news.php">
                <span class="material-icons-outlined">upgrade</span> Update News
            </a>
        </li>
        <li class="sidebar-list-item">
            <a class="title" href="activity-log.php">
                <span class="material-icons-outlined">dvr</span> Activity Log
            </a>
        </li>
        <li class="sidebar-list-item">
            <a class="logout title" href="javascript:void(0);" onclick="confirmLogout()">
                <span class="material-icons-outlined">logout</span> Logout
            </a>
        </li>
    </ul>
</aside>
<!-- End Sidebar -->

<style>

</style>

<script>
    function toggleSubmenu(event, submenuId) {
        event.preventDefault();
        const submenus = document.querySelectorAll('.submenu');
        submenus.forEach(submenu => {
            if (submenu.id !== submenuId) {
                submenu.style.display = 'none';
            }
        });
        const submenu = document.getElementById(submenuId);
        submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
    }

    // Initialize submenus to be hidden
    document.getElementById('process-submenu').style.display = 'none';
    document.getElementById('report-submenu').style.display = 'none';
    document.getElementById('manage-submenu').style.display = 'none';
</script>