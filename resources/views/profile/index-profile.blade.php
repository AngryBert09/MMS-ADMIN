<!DOCTYPE html>
<html lang="en">

@include('layout.headerAssets')

<body>

    <div class="main-wrapper">
        @include('layout.navbar')
        @include('layout.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="row justify-content-lg-center">
                    <div class="col-lg-10">
                        @include('layout.breadcrumb')
                        <div class="profile-cover">
                            <div class="profile-cover-wrap">
                                <img class="profile-cover-img" src="{{ asset('img/greatwall-cover.jpg') }}"
                                    alt="Profile Cover">

                                <div class="cover-content">
                                    <div class="custom-file-btn">
                                        <input type="file" class="custom-file-btn-input" id="cover_upload">
                                        <label class="custom-file-btn-label btn btn-sm btn-white" for="cover_upload">
                                            <i class="fas fa-camera"></i>
                                            <span class="d-none d-sm-inline-block ms-1">Update Cover</span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="text-center mb-5">
                            <label class="avatar avatar-xxl profile-cover-avatar" for="avatar_upload">
                                <img class="avatar-img"
                                    src="{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : asset('img/profiles/default.jpg') }}"
                                    alt="Profile Image">

                                <a href="{{ route('profile.edit') }}">
                                    <span class="avatar-edit">
                                        <i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></a></i>
                                </span>
                            </label>
                            <h2>{{ $user->name }} <i class="fas fa-certificate text-warning small"
                                    data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="Verified"></i></h2>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <i class="far fa-building"></i> <span>{{ $user->address }}</span>
                                </li>

                                <li class="list-inline-item">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>{{ $user->created_at->diffForHumans() }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card card-body">
                                    <h5>Complete your profile</h5>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="progress progress-md flex-grow-1">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                style="width: {{ $user->getProfileCompletionPercentage() }}%"
                                                aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ms-4">{{ $user->getProfileCompletionPercentage() }}%</span>
                                    </div>

                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title d-flex justify-content-between">
                                            <span>Profile</span>
                                            <a class="btn btn-sm btn-white" href="{{ route('profile.edit') }}">Edit</a>
                                        </h5>
                                    </div>

                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li class="py-0">
                                                <h6>About</h6>
                                            </li>
                                            <li>
                                                {{ $user->name }}
                                            </li>
                                            <li class="pt-2 pb-0">
                                                <h6>Contacts</h6>
                                            </li>
                                            <li>
                                                <a href="{{ route('profile.edit') }}" class="__cf_email__"
                                                    data-cfemail="72111a13001e17011a13141c170032170a131f021e175c111d1f">[email&#160;protected]</a>
                                            </li>
                                            <li>
                                                {{ $user->phone_number }}
                                            </li>
                                            <li class="pt-2 pb-0">
                                                <h6>Address</h6>
                                            </li>
                                            <li>
                                                {{ $user->address }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Activity</h5>
                                    </div>
                                    <div class="card-body card-body-height">
                                        <ul class="activity-feed">
                                            @if ($activities->count())
                                                @foreach ($activities as $activity)
                                                    <li class="feed-item">
                                                        <div class="feed-date">
                                                            {{ \Carbon\Carbon::parse($activity->created_at)->format('M d') }}
                                                        </div>
                                                        <span class="feed-text">
                                                            {!! $activity->description !!}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="feed-item">
                                                    <span class="feed-text">No recent activities found.</span>
                                                </li>
                                            @endif
                                        </ul>
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
