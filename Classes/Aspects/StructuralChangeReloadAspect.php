<?php

namespace VIVOMEDIA\StructuralChangeReload\Aspects;

use Neos\ContentRepository\Core\SharedModel\Node\NodeAddress;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Neos\Ui\Domain\Model\Feedback\Operations\ReloadContentOutOfBand;
use Neos\Neos\Ui\Domain\Model\Feedback\Operations\ReloadDocument;
use Neos\Neos\Ui\Domain\Model\Feedback\Operations\RenderContentOutOfBand;
use Neos\Neos\Ui\Domain\Model\FeedbackCollection;
use Neos\Neos\Ui\Domain\Model\FeedbackInterface;
use Neos\Neos\Ui\Domain\Model\RenderedNodeDomAddress;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class StructuralChangeReloadAspect
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

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
            $node = $feedback->getNode();
            $subgraph = $this->contentRepositoryRegistry->subgraphForNode($node);
            $nodeTypeManager = $this->contentRepositoryRegistry->get($node->contentRepositoryId)->getNodeTypeManager();

            $parentNode = $subgraph->findParentNode($node->aggregateId);
            $nodeType = $nodeTypeManager->getNodeType($parentNode->nodeTypeName);
            if (
                $nodeType->getConfiguration('options.reloadPageIfStructureHasChanged') == TRUE
            ) {
                $alternateFeedback = new ReloadDocument();
                $joinPoint->setMethodArgument('feedback', $alternateFeedback);
            } elseif (
                $nodeType->getConfiguration('options.reloadIfStructureHasChanged') == TRUE
            ) {
                $fusionContextNodeTypeTag = '<' . $parentNode->nodeTypeName->value . '>';
                $parentNodeFusionPath = explode('/', $feedback->getParentDomAddress()->getFusionPath());
                for ($i = count($parentNodeFusionPath) - 1; $i >= 0; $i--) {
                    if (strpos($parentNodeFusionPath[$i], $fusionContextNodeTypeTag) === false) {
                        array_pop($parentNodeFusionPath);
                    } else {
                        break;
                    }
                }

                $alternateFeedback = new ReloadContentOutOfBand();
                $alternateFeedback->setNode($parentNode);
                $parentNodeDomAddress = new RenderedNodeDomAddress();
                $parentNodeDomAddress->setContextPath(NodeAddress::fromNode($parentNode)->toJson());
                $parentNodeDomAddress->setFusionPath(join('/', $parentNodeFusionPath));
                $alternateFeedback->setNodeDomAddress($parentNodeDomAddress);

                $joinPoint->setMethodArgument('feedback', $alternateFeedback);
            }
        }

        return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }

}
