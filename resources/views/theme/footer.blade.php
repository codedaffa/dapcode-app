        <footer class="app-footer">
            <div class="footer-left">
                <span>&copy; {{ date('Y') }} <strong>DapCode</strong>. Hierarchical Model-View-Controller Framework.</span>
            </div>
            <div class="footer-right">
                <span class="version-tag"><i class="fa-solid fa-code-branch"></i> Laravel {{ app()->version() }} &bull; PHP {{ PHP_VERSION }}</span>
            </div>
        </footer>
    </div> {{-- End .main-wrapper --}}
</div> {{-- End .app-layout --}}

<!-- Scripts -->
<script src="{{ asset('assets/js/theme-responsive.js') }}"></script>
</body>
</html>
