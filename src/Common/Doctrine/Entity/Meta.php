<?php

namespace Common\Doctrine\Entity;

use Backend\Core\Engine\Meta as BackendMeta;
use Common\Doctrine\ValueObject\SEOFollow;
use Common\Doctrine\ValueObject\SEOIndex;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="meta", indexes={@ORM\Index(name="idx_url", columns={"url"})})
 * @ORM\Entity(repositoryClass="Common\Doctrine\Repository\MetaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Meta
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $keywords;

    /**
     * @var bool
     *
     * @ORM\Column(type="enum_bool", name="keywords_overwrite", options={"default" = "N"})
     */
    private $keywordsOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(type="enum_bool", name="description_overwrite", options={"default" = "N"})
     */
    private $descriptionOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var bool
     *
     * @ORM\Column(type="enum_bool", name="title_overwrite", options={"default" = "N"})
     */
    private $titleOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @var bool
     *
     * @ORM\Column(type="enum_bool", name="url_overwrite", options={"default" = "N"})
     */
    private $urlOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $custom;

    /**
     * @var array
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    /**
     * @param string $keywords
     * @param bool $keywordsOverwrite
     * @param string $description
     * @param bool $descriptionOverwrite
     * @param string $title
     * @param bool $titleOverwrite
     * @param string $url
     * @param bool $urlOverwrite
     * @param string $custom
     * @param array $data
     * @param int|null $id
     */
    public function __construct(
        string $keywords,
        bool $keywordsOverwrite,
        string $description,
        bool $descriptionOverwrite,
        string $title,
        bool $titleOverwrite,
        string $url,
        bool $urlOverwrite,
        string $custom,
        array $data,
        int $id = null
    ) {
        $this->keywords = $keywords;
        $this->keywordsOverwrite = $keywordsOverwrite;
        $this->description = $description;
        $this->descriptionOverwrite = $descriptionOverwrite;
        $this->title = $title;
        $this->titleOverwrite = $titleOverwrite;
        $this->url = $url;
        $this->urlOverwrite = $urlOverwrite;
        $this->custom = $custom;
        $this->data = $data;
        $this->id = $id;
    }

    /**
     * @param string $keywords
     * @param bool $keywordsOverwrite
     * @param string $description
     * @param bool $descriptionOverwrite
     * @param string $title
     * @param bool $titleOverwrite
     * @param string $url
     * @param bool $urlOverwrite
     * @param string $custom
     * @param array $data
     */
    public function update(
        string $keywords,
        bool $keywordsOverwrite,
        string $description,
        bool $descriptionOverwrite,
        string $title,
        bool $titleOverwrite,
        string $url,
        bool $urlOverwrite,
        string $custom,
        array $data
    ) {
        $this->keywords = $keywords;
        $this->keywordsOverwrite = $keywordsOverwrite;
        $this->description = $description;
        $this->descriptionOverwrite = $descriptionOverwrite;
        $this->title = $title;
        $this->titleOverwrite = $titleOverwrite;
        $this->url = $url;
        $this->urlOverwrite = $urlOverwrite;
        $this->custom = $custom;
        $this->data = $data;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function serialiseData()
    {
        if (!empty($this->data)) {
            if (array_key_exists('seo_index', $this->data)) {
                $this->data['seo_index'] = (string) $this->data['seo_index'];
            }
            if (array_key_exists('seo_follow', $this->data)) {
                $this->data['seo_follow'] = (string) $this->data['seo_follow'];
            }
            $this->data = serialize($this->data);

            return;
        }

        $this->data = null;
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     * @ORM\PostLoad
     */
    public function unSerialiseData()
    {
        if ($this->data === null) {
            $this->data = [];

            return;
        }

        // backwards compatible fix for when the seo is saved with the serialized value objects
        // @todo remove this for when all the modules use doctrine
        $this->data = preg_replace(
            '$O\\:3[67]\\:"Common\\\\Doctrine\\\\ValueObject\\\\(?:(?:SEOIndex)|(?:SEOFollow))"\\:1\\:{s\\:4[68]\\:"\\x00Common\\\\Doctrine\\\\ValueObject\\\\(?:(?:SEOIndex)|(?:SEOFollow))\\x00(?:(?:SEOIndex)|(?:SEOFollow))";(s\\:\d+\\:".+?";)}$',
            '$1',
            $this->data
        );
        $this->data = unserialize($this->data);
        if (array_key_exists('seo_index', $this->data)) {
            $this->data['seo_index'] = SEOIndex::fromString($this->data['seo_index']);
        }
        if (array_key_exists('seo_follow', $this->data)) {
            $this->data['seo_follow'] = SEOFollow::fromString($this->data['seo_follow']);
        }
    }

    /**
     * @param BackendMeta $meta
     *
     * @return self
     */
    public static function fromBackendMeta(BackendMeta $meta): self
    {
        $metaData = $meta->getData();

        return new self(
            $metaData['keywords'],
            $metaData['keywords_overwrite'] === 'Y',
            $metaData['description'],
            $metaData['description_overwrite'] === 'Y',
            $metaData['title'],
            $metaData['title_overwrite'] === 'Y',
            $metaData['url'],
            $metaData['url_overwrite'] === 'Y',
            $metaData['custom'],
            $metaData['data'] ?? [],
            $meta->getId()
        );
    }

    /**
     * Used in the transformer of the Symfony form type for this entity
     *
     * @param array $metaData
     *
     * @return self
     */
    public static function updateWithFormData(array $metaData): self
    {
        return new self(
            $metaData['keywords'],
            $metaData['keywordsOverwrite'],
            $metaData['description'],
            $metaData['descriptionOverwrite'],
            $metaData['title'],
            $metaData['titleOverwrite'],
            $metaData['url'],
            $metaData['urlOverwrite'],
            $metaData['custom'] ?? null,
            [
                'seo_index' => SEOIndex::fromString($metaData['SEOIndex']),
                'seo_follow' => SEOFollow::fromString($metaData['SEOFollow']),
            ],
            (int) $metaData['id']
        );
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @return bool
     */
    public function isKeywordsOverwrite(): bool
    {
        return $this->keywordsOverwrite;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isDescriptionOverwrite(): bool
    {
        return $this->descriptionOverwrite;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return bool
     */
    public function isTitleOverwrite(): bool
    {
        return $this->titleOverwrite;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isUrlOverwrite(): bool
    {
        return $this->urlOverwrite;
    }

    /**
     * @return string
     */
    public function getCustom(): string
    {
        return $this->custom;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function hasSEOIndex(): bool
    {
        return array_key_exists('seo_index', $this->data)
               && !SEOIndex::fromString($this->data['seo_index'])->isNone();
    }

    /**
     * @return SEOIndex|null
     */
    public function getSEOIndex()
    {
        if (!$this->hasSEOIndex()) {
            return;
        }

        return SEOIndex::fromString($this->data['seo_index']);
    }

    /**
     * @return bool
     */
    public function hasSEOFollow(): bool
    {
        return array_key_exists('seo_follow', $this->data)
               && !SEOFollow::fromString($this->data['seo_follow'])->isNone();
    }

    /**
     * @return SEOFollow|null
     */
    public function getSEOFollow()
    {
        if (!$this->hasSEOFollow()) {
            return;
        }

        return SEOFollow::fromString($this->data['seo_follow']);
    }
}
