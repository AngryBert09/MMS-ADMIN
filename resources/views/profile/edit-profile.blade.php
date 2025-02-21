<!DOCTYPE html>
<html lang="en">

@include('layout.headerAssets')

<body>

    <div class="main-wrapper">

        @include('layout.navbar')
        @include('layout.sidebar')


        <div class="page-wrapper">
            <div class="content container-fluid">
                @include('layout.breadcrumb')
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="widget settings-menu">
                            <ul>
                                <li class="nav-item">
                                    <a href="{{ route('profile.edit') }}" class="nav-link active">
                                        <i class="far fa-user"></i> <span>Profile Settings</span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('profile.change-pass') }}" class="nav-link">
                                        <i class="fas fa-unlock-alt"></i> <span>Change Password</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                    <div class="col-xl-9 col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Basic information</h5>
                            </div>
                            <div class="card-body">
                                @include('profile.message')
                                <form action="{{ route('profile.update', $user->id) }} " method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row form-group">
                                        <label for="name" class="col-sm-3 col-form-label input-label">Profile
                                            Image</label>
                                        <div class="col-sm-9">
                                            <div class="d-flex align-items-center">
                                                <label class="avatar avatar-xxl profile-cover-avatar m-0"
                                                    for="edit_img">
                                                    <img id="avatarImg" class="avatar-img"
                                                        src="{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : asset('img/profiles/default.jpg') }}"
                                                        alt="Profile Image">
                                                    <input type="file" id="edit_img" name="profile_pic">
                                                    <span class="avatar-edit">
                                                        <i data-feather="edit-2"
                                                            class="avatar-uploader-icon shadow-soft"></i>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>

                                        <script>
                                            document.getElementById('edit_img').addEventListener('change', function(event) {
                                                var input = event.target;
                                                if (input.files && input.files[0]) {
                                                    var reader = new FileReader();
                                                    reader.onload = function(e) {
                                                        document.getElementById('avatarImg').src = e.target.result;
                                                    }
                                                    reader.readAsDataURL(input.files[0]);
                                                }
                                            });
                                        </script>

                                    </div>
                                    <div class="row form-group">
                                        <label for="name" class="col-sm-3 col-form-label input-label">Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="Your Name" value="{{ $user->name }}">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="email" class="col-sm-3 col-form-label input-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Email" value="{{ $user->email }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="phone" class="col-sm-3 col-form-label input-label">Phone <span
                                                class="text-muted"></span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="phone"
                                                name="phone_number" placeholder="+x(xxx)xxx-xx-xx"
                                                value="{{ $user->phone_number }}" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="addressline1"
                                            class="col-sm-3 col-form-label input-label">Address</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="addressline1" name="address"
                                                placeholder="Your address" value="{{ $user->address }}">
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-warning">Save Changes</button>
                                    </div>
                                </form>


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
