<?php

namespace FSC\HateoasBundle\Factory;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FSC\HateoasBundle\Routing\RelationUrlGenerator;

use FSC\HateoasBundle\Model\Link;

abstract class AbstractLinkFactory
{
    protected $relationUrlGenerator;
    protected $forceAbsolute;
    protected $request;

    public function __construct(RelationUrlGenerator $relationUrlGenerator, $forceAbsolute = true)
    {
        $this->relationUrlGenerator = $relationUrlGenerator;
        $this->forceAbsolute = $forceAbsolute;
    }

    public static function createLink($rel, $href, $attributes = null)
    {
        $link = new Link();
        $link->setRel($rel);
        $link->setHref($href);
        $link->setAttributes($attributes);

        return $link;
    }

    public function setRequest(RequestStack $request_stack)
    {
        $this->request = $request_stack->getCurrentRequest();
    }

    public function generateUrl($name, $parameters = array(), $options = array())
    {
        $parameters += array(
            "_format" => $this->request->getRequestFormat(),
            "version" => $this->request->get('version')
        );

        ksort($parameters); // Have consistent url query strings, for the tests

        $alias = !empty($options['router']) ? $options['router'] : 'default';
        $urlGenerator = $this->relationUrlGenerator->getUrlGenerator($alias);

        $absolute = isset($options['absolute']) ? $options['absolute'] : $this->forceAbsolute;

        return $urlGenerator->generate($name, $parameters, $absolute);
    }
}
