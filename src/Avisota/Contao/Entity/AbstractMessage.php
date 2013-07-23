<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Entity;

use Avisota\Contao\Event\ResolveStylesheetEvent;
use Contao\Doctrine\ORM\AliasableInterface;
use Contao\Doctrine\ORM\Entity;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractMessage extends Entity implements AliasableInterface
{
	/**
	 * @var string
	 */
	protected $language;

	function __construct()
	{
		if (isset($GLOBALS['TL_LANGUAGE'])) {
			$this->language = $GLOBALS['TL_LANGUAGE'];
		}
	}

	/**
	 * Get recipients
	 *
	 * @return RecipientSource
	 */
	public function getRecipients()
	{
		$category = $this->getCategory();

		if ($category->getBoilerplates() ||
			$category->getRecipientMode() == 'byMessage'
		) {
			$recipients = $this->recipients;
		}
		else if ($category->getRecipientMode() == 'byMessageOrCategory') {
			$recipients = $this->recipients;
			if (!$recipients) {
				$recipients = $category->getRecipients();
			}
		}
		else if ($category->getRecipientMode() == 'byCategory') {
			$recipients = $category->getRecipients();
		}
		else {
			throw new \RuntimeException('Could not find recipients for message ' . $this->getId());
		}

		return $this->callGetterCallbacks('recipients', $recipients);
	}

	/**
	 * Get layout
	 *
	 * @return Layout
	 */
	public function getLayout()
	{
		$category = $this->getCategory();

		if ($category->getBoilerplates() ||
			$category->getLayoutMode() == 'byMessage'
		) {
			$layout = $this->layout;
		}
		else if ($category->getLayoutMode() == 'byMessageOrCategory') {
			$layout = $this->layout;
			if (!$layout) {
				$layout = $category->getLayout();
			}
		}
		else if ($category->getLayoutMode() == 'byCategory') {
			$layout = $category->getLayout();
		}
		else {
			throw new \RuntimeException('Could not find layout for message ' . $this->getId());
		}

		return $this->callGetterCallbacks('layout', $layout);
	}

    /**
     * Get transport
     *
     * @return Transport
     */
    public function getTransport()
    {
		$category = $this->getCategory();

		if ($category->getBoilerplates() ||
			$category->getTransportMode() == 'byMessage'
		) {
			$transport = $this->transport;
		}
		else if ($category->getTransportMode() == 'byMessageOrCategory') {
			$transport = $this->transport;
			if (!$transport) {
				$transport = $category->getTransport();
			}
		}
		else if ($category->getTransportMode() == 'byCategory') {
			$transport = $category->getTransport();
		}
		else {
			throw new \RuntimeException('Could not find transport for message ' . $this->getId());
		}

		return $this->callGetterCallbacks('transport', $transport);
    }

	/**
	 * {@inheritdoc}
	 */
	public function getAliasParentValue()
	{
		return $this->getSubject();
	}
}
