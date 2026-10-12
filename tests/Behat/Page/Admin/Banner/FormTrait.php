<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner;

trait FormTrait
{
    public function specifyPosition(int $position): void
    {
        $this->getElement('position')->setValue((string) $position);
    }

    public function checkChannel(string $name): void
    {
        $this->getElement('channels')->checkField($name);
    }

    public function attachImage(string $path, string $localeCode): void
    {
        $filesPath = $this->getParameter('files_path');

        $this->getElement('image', ['%locale%' => $localeCode])->attachFile($filesPath . $path);
    }

    public function specifyMainText(string $mainText, string $localeCode): void
    {
        $this->getElement('main_text', ['%locale%' => $localeCode])->setValue($mainText);
    }

    public function disable(): void
    {
        $this->getElement('enabled')->uncheck();
    }

    public function getErrorOf(string $element, string $localeCode = ''): string
    {
        $field = $this->getElement($element, ['%locale%' => $localeCode]);

        return (string) $field->getParent()->find('css', '.invalid-feedback')?->getText();
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        /** @var array<string, string> $elements */
        $elements = parent::getDefinedElements();

        return array_merge($elements, [
            'channels' => '#banner_channels',
            'code' => '#banner_code',
            'enabled' => '#banner_enabled',
            'image' => '#banner_translations_%locale%_imageFile',
            'main_text' => '#banner_translations_%locale%_mainText',
            'position' => '#banner_position',
        ]);
    }
}
