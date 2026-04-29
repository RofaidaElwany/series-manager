<?php

namespace Admin\Pages;

if (!defined('ABSPATH')) {
    exit;
}
class Layouts
{
    public static function render()
    {
?>
        <main class="pt-12 min-h-screen bg-surface">
    <div class="px-8 max-w-6xl mx-auto">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="font-display text-display-md text-on-surface mb-2">
                Post Layout Settings
            </h1>
            <p class="font-body text-body-md text-on-surface-variant">
                Configure how your content appears across the front-end of your website.
            </p>
        </div>

        <!-- Content Section -->
        <section class="bg-surface-container-lowest rounded-lg border border-outline-variant p-6 shadow-sm">

            <div class="mb-6">
                <h2 class="font-display text-headline-md text-on-surface">
                    Choose Your Post Layout
                </h2>
            </div>

            <!-- Layout Options -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                <!-- Accordion Layout -->
                <div class="group relative flex flex-col bg-surface-container-lowest border border-outline-variant rounded-lg overflow-hidden cursor-pointer hover:border-gray-400 hover:shadow-md transition-all">

                    <div class="aspect-video bg-surface-container-low flex items-center justify-center p-4 overflow-hidden">
                        <img class="w-full h-full object-cover rounded opacity-80 group-hover:opacity-100 transition"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBoE2qkyZTpVTo_YQIjtnmIi6QSHAMxs7YZ7zOFpVdqS7F6BbZnVx6It88LJDkVtg3a9PXmuHXNfd6A1wC2TsN6t4i6exmVM7rIfo9bcBU9aWBunfOdpM0aBvHgue0SBnFPfs2u-vXs9mvW_XgrvHYMHys4DNonHxq5tkN-BNsqvIcpWiYBQvr3rh96o9McKPMbDNGXbeA67wVKFeLSpT0EC_F-T_I2OENVU51c1niMD8Nkdtp3uV9O8BAyjKzjGlzml8X0qMBHPg"/>
                    </div>

                    <div class="p-4 flex-1">
                        <h3 class="font-display text-title-md text-on-surface mb-1">
                            Accordion Layout
                        </h3>
                        <p class="font-body text-body-sm text-on-surface-variant">
                            Display posts in an expandable accordion view, ideal for FAQs or archives.
                        </p>
                    </div>
                </div>

                <!-- Grid Layout (Selected) -->
                <div class="group relative flex flex-col bg-surface-container-lowest border-2 border-primary rounded-lg overflow-hidden shadow-md cursor-pointer">

                    <!-- Selected Badge -->
                    <div class="absolute top-2 right-2 bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                        ✓
                    </div>

                    <div class="aspect-video bg-primary-container flex items-center justify-center p-4 overflow-hidden">
                        <img class="w-full h-full object-cover rounded"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoGshERoOST__f5egr6tUj57VKUOCjRiF2uxJqRxKz_yPkBkggryk7hK2DXNerLem9X48BPqmPu4Lz94V71Tc5d0DhtONyowZYzT2bDmBnxO8UqkR_eJ0ts7ooXAfRIzS-WHQIo1NuE-YmcED9--L5Chr1K5HcuLk15BqkvTkHiiFARIS8RhDzhzuniWUeLpr7mZ34tqbDqSMsYX6ZYZfb-BV97qR3OvgDCUV2QgTvyfxC1Jz9a3XxE07-wO9OrmKpUp8WNOyIPg"/>
                    </div>

                    <div class="p-4 flex-1">
                        <h3 class="font-display text-title-md text-on-surface mb-1">
                            Grid Layout
                        </h3>
                        <p class="font-body text-body-sm text-on-surface-variant">
                            A clean, modern grid layout for visual-heavy post types.
                        </p>
                    </div>
                </div>

                <!-- List Layout -->
                <div class="group relative flex flex-col bg-surface-container-lowest border border-outline-variant rounded-lg overflow-hidden cursor-pointer hover:border-gray-400 hover:shadow-md transition-all">

                    <div class="aspect-video bg-surface-container-low flex items-center justify-center p-4 overflow-hidden">
                        <img class="w-full h-full object-cover rounded opacity-80 group-hover:opacity-100 transition"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAi01Ua755WCzhybFd1-o6Z3bNBB7ghPksq-v93lC6kGw9e1E_DniWI1ExWLfVp5eTmoZHh-0IVXXWPQiYwKTt6xNo2zvDxEawRW-0PTXdUnRnRFkpgdp0lm7coLCqgw7dCKQEp1P5kidKDN5-TASIiJ42Zpif7yeq7rKaifTPcQ9v2hPED3pfY5w3oatfX6v1kM2kaas164N6yAxI8G1s--0Zmh7htD0cS9oQO4ZmMSxBWxbo6iIZbvs1_UMw4TMmm9WmeLJSg8A"/>
                    </div>

                    <div class="p-4 flex-1">
                        <h3 class="font-display text-title-md text-on-surface mb-1">
                            List Layout
                        </h3>
                        <p class="font-body text-body-sm text-on-surface-variant">
                            Classic list view with text excerpts and small thumbnails.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Action Bar -->
            <div class="pt-6 border-t border-outline-variant flex justify-between items-center">
                <p class="font-body text-label-md text-on-surface-variant">
                    Select layout changes will apply to all post archives.
                </p>

                <button class="bg-primary text-white px-6 py-2 rounded-lg font-label-md hover:bg-primary-dim transition">
                    Save Changes
                </button>
            </div>

        </section>

        <!-- Secondary Card -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-6">
                <h3 class="font-display text-headline-md text-on-surface mb-4">
                    Typography Defaults
                </h3>

                <div class="space-y-4">
                    <div class="flex flex-col gap-1">
                        <label class="font-body text-label-sm uppercase tracking-wider text-on-surface-variant">
                            Heading Font
                        </label>

                        <select class="border border-outline-variant rounded px-3 py-2 text-body-md focus:border-primary focus:ring-1 focus:ring-primary">
                            <option>System Sans-Serif</option>
                            <option>Inter</option>
                            <option>Playfair Display</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>
<?php
    }
}
