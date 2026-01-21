<?php
/**
 * Search form template
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="relative">
        <input
            type="search"
            class="input pr-10"
            placeholder="Rechercher..."
            value="<?php echo get_search_query(); ?>"
            name="s"
        >
        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-dark-400 hover:text-primary-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </button>
    </div>
</form>
