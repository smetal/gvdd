<?php

/**
 * RokGallery_Model_File
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    RokGallery
 * @subpackage models
 * @author     RocketTheme LLC <support@rockettheme.com>
 * @version    SVN: $Id: File.php 39574 2011-07-06 10:05:24Z btowles $
 */
class RokGallery_Model_File extends RokGallery_Model_Base_File
{

    /**
     * @param Doctrine_Event $event
     */
    public function preDelete($event)
    {
        // Queue the directory for deletion
        RokGallery_Queue_DirectoryDelete::add($this->getDirectoryPath());
    }

    /**
     * @param Doctrine_Event $event
     */
    public function preUpdate($event)
    {
        $record = $event->getInvoker();
        $modified = $record->getModified();
        $original = $record->getModified(true);

        foreach ($this->Slices as &$slice)
        {
            if (array_key_exists('title', $modified)) {
                if ($slice->title == $original['title'] || $slice->title == null) {
                    $slice->title = $modified['title'];
                }
            }
            if (array_key_exists('slug', $modified)) {
                if ($slice->slug == $original['slug'] || $slice->slug == null) {
                    $slice->slug = $modified['slug'];
                }
            }
            if (array_key_exists('description', $modified)) {
                if ($slice->caption == $original['description'] || $slice->caption == null) {
                    $slice->caption = $modified['description'];
                }
            }
            if (array_key_exists('published', $modified)
                && $modified['published'] == false
                && $original['published'] == true
            ) {
                $slice->published = false;
            }

            if (array_key_exists('published', $modified)
                && $modified['published'] == true
                && $original['published'] == false
                && RokGallery_Config::getOption(RokGallery_Config::OPTION_SLICE_AUTOPUBLISH_ON_FILE_PUBLISH, false)
            ) {
                $slice->published = true;
            }
        }

        if (array_key_exists('manipulations', $modified)) {
            $this->processImages();
        }
    }

    public function postDelete($event)
    {

        //Make sure all linked tables are clean

        //RokGallery_Model_FileTags
        $q = Doctrine_Query::create()
            ->delete('RokGallery_Model_FileTags ft')
            ->andWhere('ft.file_id NOT IN (SELECT f.id from RokGallery_Model_File f)');
        $q->execute();
        $q->free();

        //RokGallery_Model_FileViews
        $q = Doctrine_Query::create()
            ->delete('RokGallery_Model_FileViews fv')
            ->andWhere('fv.file_id NOT IN (SELECT f.id from RokGallery_Model_File f)');
        $q->execute();
        $q->free();

        //RokGallery_Model_FileLoves
        $q = Doctrine_Query::create()
            ->delete('RokGallery_Model_FileLoves fl')
            ->andWhere('fl.file_id NOT IN (SELECT f.id from RokGallery_Model_File f)');
        $q->execute();
        $q->free();

        //rokgallery_files_index
        $conn = Doctrine_Manager::connection();
        $dbh = $conn->getDbh();
        $stmt = $dbh->prepare('delete from ' . RokCommon_Doctrine::getPlatformInstance()->setTableName('rokgallery_files_index')
                . ' where id NOT IN (SELECT f.id from ' . RokGallery_Model_FileTable::getInstance()->getTableName() . ' f)');
        $stmt->execute();

        //RokGallery_Model_Slice
        $q = Doctrine_Query::create()
            ->delete('RokGallery_Model_Slice s')
            ->andWhere('s.file_id NOT IN (SELECT f.id from RokGallery_Model_File f)');
        $q->execute();
        $q->free();

    }

    /**
     * @param $filename
     * @param $path
     * @param null $title
     * @param null $description
     * @return RokGallery_Model_File
     */
    public static function &createNew($filename, $path, $title = null, $description = null)
    {
        $file = new RokGallery_Model_File();

        $file->filename = $filename;
        $file->guid = RokGallery_Helper::createUUID();

        if (!(file_exists($path) && is_readable($path)))
            throw new RokGallery_Exception(rc__('ROKGALLERY_UNABLE_TO_GET_MD5_OF_FILE_N', $path));
        $file->md5 = md5_file($path);

        if (empty($title)) {
            $title = @pathinfo($filename, PATHINFO_FILENAME);
            $title = str_replace('_', ' ', $title);
        }
        $file->title = $title;

        $file->filesize = @filesize($path);
        $file->type = strtolower(@pathinfo($filename, PATHINFO_EXTENSION));
        $file->description = $description;

        return $file;

    }

    /**
     * @return RokGallery_Model_Slice
     */
    public function &getAdminThumbSlice()
    {
        $thumb_slice = null;
        foreach ($this->Slices as &$slice)
        {
            /** @var RokGallery_Model_Slice $slice */
            if ($slice->admin_thumb == true) {
                $thumb_slice = $slice;
                break;
            }
        }
        return $thumb_slice;
    }

    /**
     * Returns the current full path to the file
     * @return string
     */
    public function getFullPath()
    {
        // Copy file to fine directory
        $basepath = $this->getDirectoryPath();
        return $basepath . DS . $this->filename;
    }

    /**
     * @return string
     */
    public function getDirectoryPath()
    {
        return RokGallery_Config::getOption(RokGallery_Config::OPTION_ROOT_PATH) . RokGallery_Helper::getPathFromGUID($this->guid, DS);
    }

    /**
     * Returns the relative path to the slice image
     *
     * @param string $seperator
     * @return string
     */
    public function getRelativePath($seperator = '/')
    {
        return RokGallery_Helper::getPathFromGUID($this->guid, $seperator) . $seperator . $this->filename;
    }

    /**
     * Get all slices with the passed tag
     * @param $tag
     * @return RokGallery_Model_Slice[]
     */
    public function getSlicesWithTag($tag)
    {
        $ret = array();
        foreach ($this->Slices as $slice)
        {
            /** @var RokGallery_Model_Slice $slice */
            if ($slice->hasTag($tag)) {
                $ret[] = $slice;
            }
        }

        if (empty($ret)) {
            $ret = false;
        }
        return $ret;
    }

    /**
     * Get all slices with the passed tag
     * @param $tag
     * @return RokGallery_Model_Slice[]
     */
    public function getSlicesForGallery($gallery_id)
    {
        $ret = array();
        foreach ($this->Slices as $slice)
        {
            /** @var RokGallery_Model_Slice $slice */
            if ($slice->gallery_id == $gallery_id) {
                $ret[] = $slice;
            }
        }

        if (empty($ret)) {
            $ret = false;
        }
        return $ret;
    }

    /**
     * @param RokGallery_Model_Gallery $gallery
     */
    public function updateSlicesForGallery(RokGallery_Model_Gallery &$gallery)
    {
        try
        {


            $manipulations = array();
            if ($gallery->keep_aspect) {
                $gallery_aspect_ratio = $gallery->width / $gallery->height;
                $image_aspect_ratio = $this->xsize / $this->ysize;
                $resize_height = $gallery->height;
                $resize_width = $gallery->width;
                if ($image_aspect_ratio < $gallery_aspect_ratio) // taller image
                {
                    $resize_height = $gallery->height;
                    $resize_width = (int)round($gallery->height * $image_aspect_ratio);
                }
                elseif ($image_aspect_ratio > $gallery_aspect_ratio) // wider image
                {
                    $resize_width = $gallery->width;
                    $resize_height = (int)round($gallery->width / $image_aspect_ratio);
                }
                $manipulations[] = new RokGallery_Manipulation_Action_Resize(array('width' => $resize_width, 'height' => $resize_height));
            }
            else
            {

                // Create thumbnail but dont make thumbnail keep the aspect ratio
                $source_aspect_ratio = $this->xsize / $this->ysize;
                $desired_aspect_ratio = $gallery->width / $gallery->height;

                $temp_width = $gallery->width;
                $temp_height = $gallery->height;
                $temp_left = 0;
                $temp_top = 0;

                if ($source_aspect_ratio > $desired_aspect_ratio) // wider image
                {
                    $temp_height = $gallery->height;
                    $temp_width = ( int )round($gallery->height * $source_aspect_ratio);
                    $temp_left = (int)round(($temp_width - $gallery->width) / 2);
                    $temp_top = 0;

                }
                elseif ($source_aspect_ratio < $desired_aspect_ratio) // taller image
                {
                    $temp_width = $gallery->width;
                    $temp_height = ( int )round($gallery->width / $source_aspect_ratio);
                    $temp_left = 0;
                    $temp_top = (int)round(($temp_height - $gallery->height) / 2);
                }

                $manipulations[] = new RokGallery_Manipulation_Action_Resize(array('width' => $temp_width, 'height' => $temp_height));
                $manipulations[] = new RokGallery_Manipulation_Action_Crop(array('left' => $temp_left, 'top' => $temp_top, 'width' => $gallery->width, 'height' => $gallery->height));
            }

            $slices = $this->getSlicesForGallery($gallery->id);
            if ($slices === false) {
                $slices = array();
                $slice = RokGallery_Model_Slice::createNew($this, $this->title, $this->description, $gallery->auto_publish);
                $slice->Gallery = $gallery;
                $slices[] = $slice;
            }

            foreach ($slices as &$slice)
            {
                // skip changing manipulations if force_image_size is set and image size is the same  position could have been modified
                if (!$gallery->keep_aspect && $gallery->force_image_size && $gallery->width == $slice->xsize && $gallery->height == $slice->ysize) {
                    $manipulations = $slice->manipulations;
                }
                if ($slice->Gallery->id == $gallery->id) {
                    $slice->manipulations = $manipulations;
                    $slice->save();
                    if (!file_exists($slice->getFullPath())
                            || $gallery->thumb_xsize != $slice->thumb_xsize
                            || $gallery->thumb_ysize != $slice->thumb_ysize
                            || $gallery->thumb_keep_aspect != $slice->thumb_keep_aspect
                            || $gallery->thumb_background != $slice->thumb_background
                    ) {
                        $slice->thumb_xsize = $gallery->thumb_xsize;
                        $slice->thumb_ysize = $gallery->thumb_ysize;
                        $slice->thumb_keep_aspect = $gallery->thumb_keep_aspect;
                        $slice->thumb_background = $gallery->thumb_background;
                        $slice->generateThumbnail($gallery->thumb_xsize, $gallery->thumb_ysize, $gallery->thumb_keep_aspect, $gallery->thumb_background);
                        $slice->save();
                    }

                }
            }
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /**
     */
    public function incrementView()
    {
        if (null == $this->Views) $this->Views = new RokGallery_Model_FileViews();
        $this->Views->count++;
        $this->Views->save();
    }

    /**
     */
    public function incrementLoves()
    {
        if (null == $this->Loves) $this->Loves = new RokGallery_Model_FileLoves();
        $this->Loves->count++;
        $this->Loves->save();
    }

    /**
     */
    public function decrementLoves()
    {
        if (null == $this->Loves) $this->Loves = new RokGallery_Model_FileLoves();
        if ($this->Loves->count > 0) {
            $this->Loves->count--;
            $this->Loves->save();
        }
    }
}