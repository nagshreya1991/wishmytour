<!DOCTYPE html>
<html lang="en">

@include('backend.layouts.head')

<body>
<!-- Preloader -->
<div class="preloader-it">
    <div class="la-anim-1"></div>
</div>
<!-- /Preloader -->
<div class="wrapper theme-1-active pimary-color-red">
    <!-- Top Menu Items -->
    @include('backend.layouts.header')
    <!-- /Top Menu Items -->

    <!-- Left Sidebar Menu -->
    @include('backend.layouts.sidebar')
    <!-- /Left Sidebar Menu -->

    <!-- Main Content -->
    <div class="page-wrapper">
        <div class="container-fluid pt-25">
@yield('main-content')

@include('backend.layouts.footer')

</body>

</html>
