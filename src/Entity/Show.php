<?php

namespace CascadePublicMedia\PbsApiExplorer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CascadePublicMedia\PbsApiExplorer\Repository\ShowRepository")
 */
class Show
{
    /**
     * The Media Manage API endpoint for this entity.
     */
    public const ENDPOINT = 'shows';

    /**
     * The human readable name for this class.
     */
    public const NAME = 'show';

    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleSortable;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $nola;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tmsId;

    /**
     * @ORM\Column(type="string", length=90)
     */
    private $descriptionShort;

    /**
     * @ORM\Column(type="text")
     */
    private $descriptionLong;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $premiered;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dfpExclude;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $funderMessage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gaTrackingPage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gaTrackingEvent;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hashtag;

    /**
     * @ORM\Column(type="boolean")
     */
    private $displayEpisodeNumber;

    /**
     * @ORM\Column(type="boolean")
     */
    private $canEmbedPlayer;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $language;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ordinalSeasons;

    /**
     * @ORM\Column(type="boolean")
     */
    private $episodeSortDesc;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="CascadePublicMedia\PbsApiExplorer\Entity\Genre",
     *     inversedBy="shows"
     * )
     */
    private $genre;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="CascadePublicMedia\PbsApiExplorer\Entity\Franchise",
     *     inversedBy="shows"
     * )
     */
    private $franchise;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $episodeCount;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $links = [];

    /**
     * @ORM\ManyToMany(
     *     targetEntity="CascadePublicMedia\PbsApiExplorer\Entity\Audience",
     *     inversedBy="shows",
     *     cascade={"persist", "merge"}
     *)
     */
    private $audience;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="CascadePublicMedia\PbsApiExplorer\Entity\Platform",
     *     inversedBy="shows",
     *     cascade={"persist", "merge"}
     *)
     */
    private $platform;

    /**
     * @ORM\OneToMany(
     *     targetEntity="CascadePublicMedia\PbsApiExplorer\Entity\Season",
     *     mappedBy="show",
     *     orphanRemoval=true,
     *     cascade={"persist", "merge"}
     *)
     */
    private $seasons;

    /**
     * @ORM\OneToMany(
     *     targetEntity="CascadePublicMedia\PbsApiExplorer\Entity\Episode",
     *     mappedBy="show",
     *     orphanRemoval=true,
     *     cascade={"persist", "merge"}
     * )
     */
    private $episodes;

    /**
     * @ORM\OneToMany(
     *     targetEntity="CascadePublicMedia\PbsApiExplorer\Entity\Special",
     *     mappedBy="show",
     *     orphanRemoval=true,
     *     cascade={"persist", "merge"}
     * )
     */
    private $specials;

    /**
     * @ORM\OneToMany(
     *     targetEntity="CascadePublicMedia\PbsApiExplorer\Entity\Asset",
     *     mappedBy="show",
     *     orphanRemoval=true,
     *     cascade={"persist", "merge"}
     * )
     */
    private $assets;

    /**
     * @ORM\OneToMany(targetEntity="CascadePublicMedia\PbsApiExplorer\Entity\Image", mappedBy="show")
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="CascadePublicMedia\PbsApiExplorer\Entity\RemoteAsset", mappedBy="show")
     */
    private $remoteAssets;

    public function __construct()
    {
        $this->audience = new ArrayCollection();
        $this->platform = new ArrayCollection();
        $this->seasons = new ArrayCollection();
        $this->episodes = new ArrayCollection();
        $this->specials = new ArrayCollection();
        $this->assets = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->remoteAssets = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitleSortable(): ?string
    {
        return $this->titleSortable;
    }

    public function setTitleSortable(string $titleSortable): self
    {
        $this->titleSortable = $titleSortable;

        return $this;
    }

    public function getNola(): ?string
    {
        return $this->nola;
    }

    public function setNola(?string $nola): self
    {
        $this->nola = $nola;

        return $this;
    }

    public function getTmsId(): ?string
    {
        return $this->tmsId;
    }

    public function setTmsId(?string $tmsId): self
    {
        $this->tmsId = $tmsId;

        return $this;
    }

    public function getDescriptionShort(): ?string
    {
        return $this->descriptionShort;
    }

    public function setDescriptionShort(string $descriptionShort): self
    {
        $this->descriptionShort = $descriptionShort;

        return $this;
    }

    public function getDescriptionLong(): ?string
    {
        return $this->descriptionLong;
    }

    public function setDescriptionLong(string $descriptionLong): self
    {
        $this->descriptionLong = $descriptionLong;

        return $this;
    }

    public function getPremiered(): ?\DateTimeInterface
    {
        return $this->premiered;
    }

    public function setPremiered(?\DateTimeInterface $premiered): self
    {
        $this->premiered = $premiered;

        return $this;
    }

    public function getDfpExclude(): ?bool
    {
        return $this->dfpExclude;
    }

    public function setDfpExclude(bool $dfpExclude): self
    {
        $this->dfpExclude = $dfpExclude;

        return $this;
    }

    public function getFunderMessage(): ?string
    {
        return $this->funderMessage;
    }

    public function setFunderMessage(?string $funderMessage): self
    {
        $this->funderMessage = $funderMessage;

        return $this;
    }

    public function getGaTrackingPage(): ?string
    {
        return $this->gaTrackingPage;
    }

    public function setGaTrackingPage(?string $gaTrackingPage): self
    {
        $this->gaTrackingPage = $gaTrackingPage;

        return $this;
    }

    public function getGaTrackingEvent(): ?string
    {
        return $this->gaTrackingEvent;
    }

    public function setGaTrackingEvent(?string $gaTrackingEvent): self
    {
        $this->gaTrackingEvent = $gaTrackingEvent;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getHashtag(): ?string
    {
        return $this->hashtag;
    }

    public function setHashtag(?string $hashtag): self
    {
        $this->hashtag = $hashtag;

        return $this;
    }

    public function getDisplayEpisodeNumber(): ?bool
    {
        return $this->displayEpisodeNumber;
    }

    public function setDisplayEpisodeNumber(bool $displayEpisodeNumber): self
    {
        $this->displayEpisodeNumber = $displayEpisodeNumber;

        return $this;
    }

    public function getCanEmbedPlayer(): ?bool
    {
        return $this->canEmbedPlayer;
    }

    public function setCanEmbedPlayer(bool $canEmbedPlayer): self
    {
        $this->canEmbedPlayer = $canEmbedPlayer;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getOrdinalSeasons(): ?bool
    {
        return $this->ordinalSeasons;
    }

    public function setOrdinalSeasons(bool $ordinalSeasons): self
    {
        $this->ordinalSeasons = $ordinalSeasons;

        return $this;
    }

    public function getEpisodeSortDesc(): ?bool
    {
        return $this->episodeSortDesc;
    }

    public function setEpisodeSortDesc(bool $episodeSortDesc): self
    {
        $this->episodeSortDesc = $episodeSortDesc;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getFranchise(): ?Franchise
    {
        return $this->franchise;
    }

    public function setFranchise(?Franchise $franchise): self
    {
        $this->franchise = $franchise;

        return $this;
    }

    public function getEpisodeCount(): ?int
    {
        return $this->episodeCount;
    }

    public function setEpisodeCount(?int $episodeCount): self
    {
        $this->episodeCount = $episodeCount;

        return $this;
    }

    public function getLinks(): ?array
    {
        return $this->links;
    }

    public function setLinks(?array $links): self
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @return Collection|Audience[]
     */
    public function getAudience(): Collection
    {
        return $this->audience;
    }

    public function addAudience(Audience $audience): self
    {
        if (!$this->audience->contains($audience)) {
            $this->audience[] = $audience;
        }

        return $this;
    }

    public function removeAudience(Audience $audience): self
    {
        if ($this->audience->contains($audience)) {
            $this->audience->removeElement($audience);
        }

        return $this;
    }

    /**
     * @return Collection|Platform[]
     */
    public function getPlatform(): Collection
    {
        return $this->platform;
    }

    public function addPlatform(Platform $platform): self
    {
        if (!$this->platform->contains($platform)) {
            $this->platform[] = $platform;
        }

        return $this;
    }

    public function removePlatform(Platform $platform): self
    {
        if ($this->platform->contains($platform)) {
            $this->platform->removeElement($platform);
        }

        return $this;
    }

    /**
     * @return Collection|Season[]
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setShow($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->contains($season)) {
            $this->seasons->removeElement($season);
            // set the owning side to null (unless already changed)
            if ($season->getShow() === $this) {
                $season->setShow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Episode[]
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes[] = $episode;
            $episode->setShow($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): self
    {
        if ($this->episodes->contains($episode)) {
            $this->episodes->removeElement($episode);
            // set the owning side to null (unless already changed)
            if ($episode->getShow() === $this) {
                $episode->setShow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Special[]
     */
    public function getSpecials(): Collection
    {
        return $this->specials;
    }

    public function addSpecial(Special $special): self
    {
        if (!$this->specials->contains($special)) {
            $this->specials[] = $special;
            $special->setShow($this);
        }

        return $this;
    }

    public function removeSpecial(Special $special): self
    {
        if ($this->specials->contains($special)) {
            $this->specials->removeElement($special);
            // set the owning side to null (unless already changed)
            if ($special->getShow() === $this) {
                $special->setShow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Asset[]
     */
    public function getAssets(): Collection
    {
        return $this->assets;
    }

    public function addAsset(Asset $asset): self
    {
        if (!$this->assets->contains($asset)) {
            $this->assets[] = $asset;
            $asset->setShow($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->contains($asset)) {
            $this->assets->removeElement($asset);
            // set the owning side to null (unless already changed)
            if ($asset->getShow() === $this) {
                $asset->setShow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setShow($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getShow() === $this) {
                $image->setShow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RemoteAsset[]
     */
    public function getRemoteAssets(): Collection
    {
        return $this->remoteAssets;
    }

    public function addRemoteAsset(RemoteAsset $remoteAsset): self
    {
        if (!$this->remoteAssets->contains($remoteAsset)) {
            $this->remoteAssets[] = $remoteAsset;
            $remoteAsset->setShow($this);
        }

        return $this;
    }

    public function removeRemoteAsset(RemoteAsset $remoteAsset): self
    {
        if ($this->remoteAssets->contains($remoteAsset)) {
            $this->remoteAssets->removeElement($remoteAsset);
            // set the owning side to null (unless already changed)
            if ($remoteAsset->getShow() === $this) {
                $remoteAsset->setShow(null);
            }
        }

        return $this;
    }
}
