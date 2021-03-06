<?php

namespace VIVOMEDIA\StructuralChangeReload\Aspects;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Neos\Ui\Domain\Model\Feedback\Operations\ReloadDocument;
use Neos\Neos\Ui\Domain\Model\Feedback\Operations\RenderContentOutOfBand;
use Neos\Neos\Ui\Domain\Model\FeedbackCollection;
use Neos\Neos\Ui\Domain\Model\FeedbackInterface;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class StructuralChangeReloadAspect
{
    /**
     * @Flow\Around("method(Neos\Neos\Ui\Domain\Model\FeedbackCollection->add())")
     * @param JoinPointInterface $joinPoint The current join point
     * @return mixed
     */
    public function forceFullPageReloadOnStructuralChangeInCollection(JoinPointInterface $joinPoint)
    {
        /**
         * @var FeedbackInterface $feedback
         */
        $feedback = $joinPoint->getMethodArgument('feedback');

        /**
         * @var FeedbackCollection $feedbackCollection
         */
        $feedbackCollection = $joinPoint->getProxy();

        if ($feedback instanceof RenderContentOutOfBand) {
            $parentNode = $feedback->getNode()->findParentNode();

            if (
                $parentNode->getNodeType()->getConfiguration('options.reloadPageIfStructureHasChanged') == TRUE
            ) {
                $alternateFeedback = new ReloadDocument();
                $joinPoint->setMethodArgument('feedback', $alternateFeedback);

            }
        }

        return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }

}
