<?php

namespace LittleThings;

class JsonPostRepository implements PostRepository, JsonRepository
{

    protected $datasource;

    public function __construct($JsonRepository) {
        $this->datasource = $JsonRepository;
    }

    /**
     * Creates array of posts from associative array
     *
     * @param array $posts
     * @return array
     **/
    protected function hydrate(array $posts)
    {
        return array_map(function ($post) {
            return new Post(
                $post['id'],
                $post['date'],
                $post['authorId'],
                $post['title'],
                $post['slug']
            );
        }, $posts);
    }

    /**
     * Return collection of all posts
     *
     * @return PostCollection
     */
    public function all() {
        $collection = new PostCollection($this->readJson());
        return $collection; 
    }

    /**
     * Add new post to repository
     *
     * @param Post $post
     * @return boolean
     */
    public function add(Post $post) {
        if ($this->findById($post->id)) {
            return false;
        }
        $data = $this->readJson();
        $data[sizeof($data)] = $post;
        $this->writeJson($data);
        return true;
    }

    /**
     * Find post by specific ID
     *
     * @param integer $id
     * @return Post
     */
    public function findById($id) {
        $data = $this->readJson();
        foreach ($data as $record) {
            if ($record['id'] == $id) {
                return new Post(
                    $record['id'],
                    $record['date'],
                    $record['authorId'],
                    $record['title'],
                    $record['slug']
                );
                break;
            }
        }
        return false;
    }

    /**
     * Reads Json file and returns array
     *
     * @return array
     **/
    public function readJson() {
        $data = file_get_contents($this->datasource);
        return json_decode($data, true);
    }

    /**
     * Writes data back to Json file
     *
     * @param array $data
     * @return void
     **/
    public function writeJson(array $data) {
        file_put_contents($this->datasource, json_encode($data));
    }


}