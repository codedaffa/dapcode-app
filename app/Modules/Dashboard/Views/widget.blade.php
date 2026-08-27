<div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 16px; margin-top: 16px; text-align: left;">
    <h3 style="margin-top: 0; color: #4338ca; font-size: 15px;">📊 Widget Sub-Request HMVC: {{ $widgetTitle }}</h3>
    <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #3730a3;">
        @foreach($stats as $label => $value)
            <li><strong>{{ $label }}:</strong> {{ $value }}</li>
        @endforeach
    </ul>
    <small style="display: block; margin-top: 8px; color: #6366f1; font-style: italic;">
        (Dirender secara hirarkis menggunakan <code>hmvc('Dashboard@widget')</code> dari modul lain)
    </small>
</div>
