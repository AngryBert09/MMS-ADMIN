<div class="header header-one">
    <div class="header-left header-left-one">
        <a href="{{ route('dashboard') }}" class="logo">
            <img src="{{ asset('img/greatwall-logo.png') }}" alt="Logo">
        </a>
        <a href="{{ route('dashboard') }}" class="white-logo">
            <img src="{{ asset('img/greatwall-logo.png') }}" alt="Logo">
        </a>
        <a href="{{ route('dashboard') }}" class="logo logo-small">
            <img src="{{ asset('img/greatwall-logo.png') }}" alt="Logo">
        </a>
    </div>

    <a href="javascript:void(0);" id="toggle_btn">
        <i class="fas fa-bars"></i>
    </a>
    <div class="top-nav-search">
        <form>
            <input type="text" class="form-control" placeholder="Search here">
            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <a class="mobile_btn" id="mobile_btn">
        <i class="fas fa-bars"></i>
    </a>
    <ul class="nav nav-tabs user-menu">


        <li class="nav-item dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <i data-feather="bell"></i> <span class="badge rounded-pill">0</span>
            </a>
            <div class="dropdown-menu notifications">
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Notifications</span>
                    <a href="javascript:void(0)" class="clear-noti" id="clearNotifications">Clear All</a>
                </div>
                <div class="noti-content">
                    <ul class="notification-list" id="notificationList">
                        <!-- Notifications will be loaded here dynamically -->
                    </ul>
                </div>


                <div class="topnav-dropdown-footer">
                    <a href="activities.html">View all Notifications</a>
                </div>
            </div>
        </li>


        <li class="nav-item dropdown has-arrow main-drop">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <span class="user-img">
                    <img src="{{ Auth::user()->profile_pic ? asset('storage/' . Auth::user()->profile_pic) : asset('img/profiles/default.jpg') }}"
                        alt="">
                    <span class="status online"></span>
                </span>

                <span>Admin</span>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('profile.index') }}"><i data-feather="user" class="me-1"></i>
                    Profile</a>
                <a class="dropdown-item" href="{{ route('profile.edit') }}"><i data-feather="settings"
                        class="me-1"></i>
                    Settings</a>
                <a class="dropdown-item" href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i data-feather="log-out" class="me-1"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function loadNotifications() {
            $.ajax({
                url: "{{ route('notifications.get') }}", // Corrected route
                type: "GET",
                success: function(response) {
                    let notifications = response.notifications;
                    let count = response.count;

                    // Update notification count
                    $(".badge.rounded-pill").text(count);

                    let notificationList = $("#notificationList");
                    notificationList.empty(); // Clear previous notifications

                    if (count === 0) {
                        notificationList.append(
                            '<li class="text-center p-2">No new notifications</li>'
                        );
                    } else {
                        notifications.forEach(function(notif) {
                            notificationList.append(`
                        <li class="notification-message">
                            <a href="#">
                                <div class="media d-flex">
                                    <div class="media-body">
                                        <p class="noti-details"><span class="noti-title">${notif.data.title}</span> - ${notif.data.details}</p>
                                        <p class="noti-time"><span class="notification-time">${new Date(notif.created_at).toLocaleString()}</span></p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    `);
                        });
                    }
                }
            });
        }

        // Load notifications on page load
        loadNotifications();

        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);


        // Clear notifications
        $("#clearNotifications").click(function() {
            $.ajax({
                url: "{{ route('notifications.clear') }}", // Define this route
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    $("#notificationList").empty().append(
                        '<li class="text-center p-2">No new notifications</li>');
                    $(".badge.rounded-pill").text("0");
                }
            });
        });
    });

    function loadNotifications() {
        $.ajax({
            url: "/notifications", // API route to get notifications
            type: "GET",
            dataType: "json",
            success: function(data) {
                let notificationList = $("#notificationList");
                notificationList.empty(); // Clear existing notifications

                if (data.length === 0) {
                    notificationList.append('<li class="notification-message">No new notifications.</li>');
                } else {
                    $.each(data, function(index, notification) {
                        let details = JSON.parse(notification.data); // Parse the JSON data

                        let notificationItem = `
                            <li class="notification-message">
                                <a href="#">
                                    <div class="media d-flex">
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-title rounded-circle bg-primary-light">
                                                <i class="far fa-bell"></i>
                                            </span>
                                        </div>
                                        <div class="media-body">
                                            <p class="noti-details">
                                                <span class="noti-title">${details.title}</span> - ${details.details}
                                            </p>
                                            <p class="noti-time">
                                                <span class="notification-time">${timeAgo(notification.created_at)}</span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        `;
                        notificationList.append(notificationItem);
                    });
                }
            },
            error: function() {
                console.error("Failed to fetch notifications");
            }
        });
    }

    // Convert timestamp to "x minutes ago"
    function timeAgo(timestamp) {
        let time = new Date(timestamp);
        let now = new Date();
        let diff = Math.floor((now - time) / 1000); // Difference in seconds

        if (diff < 60) return diff + " seconds ago";
        if (diff < 3600) return Math.floor(diff / 60) + " minutes ago";
        if (diff < 86400) return Math.floor(diff / 3600) + " hours ago";
        return Math.floor(diff / 86400) + " days ago";
    }

    // Load notifications every 10 seconds
    $(document).ready(function() {
        loadNotifications();
        setInterval(loadNotifications, 10000); // Refresh every 10 seconds
    });
</script>
