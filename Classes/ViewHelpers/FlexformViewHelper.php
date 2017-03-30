<?php
declare(strict_types=1);

namespace Smichaelsen\FluidFlexformViewHelper\ViewHelpers;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Usage example
 *
 * <html data-namespace-typo3-fluid="true" xmlns:ffvh="http://typo3.org/ns/Smichaelsen/FluidFlexformViewHelper/ViewHelpers">
 *  <ffvh:flexform data="{page.tx_fed_page_flexform}"> ..here you will have all flexform variables available.. </ffvh:flexform>
 * </html>
 */
class FlexformViewHelper extends AbstractViewHelper implements CompilableInterface
{

    /**
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('data', 'string', 'Flexform XML string', true);
        $this->registerArgument('as', 'string', 'Variable name for the flexform data array', false, 'flexform');
    }

    public function render(): string
    {
        return static::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    static public function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $dataArray = ArrayUtility::flatten(self::convertFlexFormContentToArray($arguments['data']));
        $templateVariables = $renderingContext->getTemplateVariableContainer();
        $templateVariables->add($arguments['as'], $dataArray);
        $content = $renderChildrenClosure();
        $templateVariables->remove($arguments['as']);
        return $content;
    }

    /**
     * @param string $flexFormContent flexForm xml string
     * @return array the processed array
     */
    static protected function convertFlexFormContentToArray(string $flexFormContent): array
    {
        if (empty($flexFormContent)) {
            return [];
        }
        $flexFormService = GeneralUtility::makeInstance(ObjectManager::class)->get(FlexFormService::class);
        return $flexFormService->convertFlexFormContentToArray($flexFormContent);
    }
}
