@if (session()->has('flash_notification.message'))
    <div class="alert alert-{{ session()->get('flash_notification.level') }}">
        <button class="close" data-close="alert"></button>
        {{ session()->get('flash_notification.message') }}
    </div>
@endif