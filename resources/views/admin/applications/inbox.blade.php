<!DOCTYPE html>
<html lang="en">

@include('layout.headerAssets')

<body>

    <div class="main-wrapper">

        @include('layout.navbar')


        @include('layout.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="page-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">Inbox</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Inbox</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-4">
                        <div class="compose-btn">
                            <a href="javascript:void(0);" class="btn btn-primary btn-block w-100">
                                Compose
                            </a>
                        </div>
                        <ul class="inbox-menu">
                            <li class="active">
                                <a href="#"><i class="fas fa-download"></i> Inbox <span
                                        class="mail-count">(5)</span></a>
                            </li>
                            <li>
                                <a href="#"><i class="far fa-star"></i> Important</a>
                            </li>
                            <li>
                                <a href="#"><i class="far fa-paper-plane"></i> Sent Mail</a>
                            </li>
                            <li>
                                <a href="#"><i class="far fa-file-alt"></i> Drafts <span
                                        class="mail-count">(13)</span></a>
                            </li>
                            <li>
                                <a href="#"><i class="far fa-trash-alt"></i> Trash</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <div class="card bg-white">
                            <div class="card-body">
                                <div class="email-header">
                                    <div class="row">
                                        <div class="col top-action-left">
                                            <div class="float-left">
                                                <div class="btn-group dropdown-action">
                                                    <button type="button" class="btn btn-white dropdown-toggle"
                                                        data-bs-toggle="dropdown">Select <i
                                                            class="fas fa-angle-down"></i></button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#">All</a>
                                                        <a class="dropdown-item" href="#">None</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="#">Read</a>
                                                        <a class="dropdown-item" href="#">Unread</a>
                                                    </div>
                                                </div>
                                                <div class="btn-group dropdown-action">
                                                    <button type="button" class="btn btn-white dropdown-toggle"
                                                        data-bs-toggle="dropdown">Actions <i
                                                            class="fas fa-angle-down"></i></button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#">Reply</a>
                                                        <a class="dropdown-item" href="#">Forward</a>
                                                        <a class="dropdown-item" href="#">Archive</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="#">Mark As Read</a>
                                                        <a class="dropdown-item" href="#">Mark As Unread</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="#">Delete</a>
                                                    </div>
                                                </div>
                                                <div class="btn-group dropdown-action">
                                                    <button type="button" class="btn btn-white dropdown-toggle"
                                                        data-bs-toggle="dropdown"><i class="fas fa-folder"></i> <i
                                                            class="fas fa-angle-down"></i></button>
                                                    <div role="menu" class="dropdown-menu">
                                                        <a class="dropdown-item" href="#">Social</a>
                                                        <a class="dropdown-item" href="#">Forums</a>
                                                        <a class="dropdown-item" href="#">Updates</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="#">Spam</a>
                                                        <a class="dropdown-item" href="#">Trash</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="#">New</a>
                                                    </div>
                                                </div>
                                                <div class="btn-group dropdown-action">
                                                    <button type="button" data-bs-toggle="dropdown"
                                                        class="btn btn-white dropdown-toggle"><i
                                                            class="fas fa-tags"></i> <i
                                                            class="fas fa-angle-down"></i></button>
                                                    <div role="menu" class="dropdown-menu">
                                                        <a class="dropdown-item" href="#">Work</a>
                                                        <a class="dropdown-item" href="#">Family</a>
                                                        <a class="dropdown-item" href="#">Social</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="#">Primary</a>
                                                        <a class="dropdown-item" href="#">Promotions</a>
                                                        <a class="dropdown-item" href="#">Forums</a>
                                                    </div>
                                                </div>
                                                <div class="btn-group dropdown-action mail-search">
                                                    <input type="text" placeholder="Search Messages"
                                                        class="form-control search-message">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto top-action-right">
                                            <div class="text-end">
                                                <button type="button" title="Refresh" data-toggle="tooltip"
                                                    class="btn btn-white d-none d-md-inline-block"><i
                                                        class="fas fa-sync-alt"></i></button>
                                                <div class="btn-group">
                                                    <a class="btn btn-white"><i class="fas fa-angle-left"></i></a>
                                                    <a class="btn btn-white"><i class="fas fa-angle-right"></i></a>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="text-muted d-none d-md-inline-block">Showing 10 of 112
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="email-content">
                                    <div class="table-responsive">
                                        <table class="table table-inbox table-hover">
                                            <thead>
                                                <tr>
                                                    <th colspan="6" class="p-3">
                                                        <input type="checkbox" class="checkbox-all">
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="email-list">
                                                @foreach ($emails as $email)
                                                    <tr onclick="openEmail({{ $email->uid }})">
                                                        <td><input type="checkbox" class="checkmail"></td>
                                                        <td><span class="mail-important"><i
                                                                    class="far fa-star"></i></span></td>
                                                        <td>
                                                            {{ is_array($email->from) ? $email->from[0]->mail ?? 'No Sender' : 'No Sender' }}
                                                        </td>

                                                        <td class="subject">{{ $email->subject }}</td>
                                                        <td><i class="fas fa-paperclip"></i></td>
                                                        {{-- <td class="mail-date">{{ $email->date->format('d M Y H:i') }} --}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>


                                        </table>

                                        <div id="email-details" class="mt-4 d-none">
                                            <div id="email-loading" class="text-center d-none">
                                                <p>Loading...</p>
                                            </div>

                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 id="email-subject"></h5>
                                                    <small class="text-muted" id="email-from"></small>
                                                </div>
                                                <div class="card-body">
                                                    <p id="email-body"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            function refreshInbox() {
                                                fetch('/inbox')
                                                    .then(response => response.text())
                                                    .then(data => {
                                                        document.getElementById("email-list").innerHTML = data;
                                                    })
                                                    .catch(error => console.error('Error fetching emails:', error));
                                            }



                                            function loadEmail(uid) {
                                                document.getElementById("email-details").classList.add("d-none");
                                                document.getElementById("email-loading").classList.remove("d-none");

                                                fetch(`/email/${uid}`)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        document.getElementById("email-loading").classList.add("d-none");

                                                        if (data.error) {
                                                            alert(data.error);
                                                        } else {
                                                            document.getElementById("email-subject").innerText = data.subject;
                                                            document.getElementById("email-from").innerText = "From: " + data.from;
                                                            document.getElementById("email-body").innerHTML = data.body;
                                                            document.getElementById("email-details").classList.remove("d-none");
                                                        }
                                                    })
                                                    .catch(error => console.error('Error:', error));
                                            }
                                        </script>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('layout.footerjs')
</body>

</html>
