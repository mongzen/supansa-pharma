<?php
namespace Application\Theme\MongZen;

use Concrete\Core\Area\Layout\Preset\Provider\ThemeProviderInterface;
use Concrete\Core\Feature\Features;
use Concrete\Core\Page\Theme\Theme;

class PageTheme extends Theme implements ThemeProviderInterface
{

    public function getThemeSupportedFeatures()
    {
        return [
            Features::BASICS,
            Features::TYPOGRAPHY,
            Features::FAQ,
            Features::NAVIGATION,
            Features::FORMS,
            Features::SEARCH,
            Features::TESTIMONIALS,
            Features::TAXONOMY,
        ];
    }

    public function registerAssets()
    {
        $this->requireAsset('font-awesome');
        $this->requireAsset('jquery');
        $this->requireAsset('vue');
        $this->requireAsset('bootstrap');
        $this->requireAsset('moment');
    }

    protected $pThemeGridFrameworkHandle = 'bootstrap3';

    public function getThemeName()
    {
        return t('MongZen');
    }

    public function getThemeDescription()
    {
        return t('A modern and responsive theme for Concrete CMS, designed with a clean aesthetic and user-friendly interface.');
    }

    /**
     * @return array
     */
    public function getThemeDefaultBlockTemplates()
    {
        return [
            'calendar' => 'bootstrap_calendar.php',
        ];
    }

    /**
     * @return array
     */
    public function getThemeAreaLayoutPresets()
    {
        $presets = [];

        return $presets;
    }
}
