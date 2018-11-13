<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Extensions
 *
 * @ORM\Table(name="extensions")
 * @ORM\Entity(repositoryClass="App\Database\Repositories\ExtentionsRepository")
 */
class Extensions
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="mime", type="string", length=20, nullable=false)
     */
    private $mime;

    /**
     * @var string
     *
     * @ORM\Column(name="editor", type="string", length=50, nullable=false)
     */
    private $editor;

    /**
     * @var string
     *
     * @ORM\Column(name="file_extension", type="string", length=50, nullable=false)
     */
    private $fileExtension;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set mime.
     *
     * @param string $mime
     *
     * @return Extensions
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Get mime.
     *
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set editor.
     *
     * @param string $editor
     *
     * @return Extensions
     */
    public function setEditor($editor)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * Get editor.
     *
     * @return string
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * Set fileExtension.
     *
     * @param string $fileExtension
     *
     * @return Extensions
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * Get fileExtension.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }
}
