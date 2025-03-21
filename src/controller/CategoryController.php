<?php

namespace controller;

require_once './src/model/CategoryModel.php';
require_once './src/abstracts/Controller.php';

use abstracts\Controller;
use model\CategoryModel;

class CategoryController extends Controller
{
    private CategoryModel $categoryModel;
    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new CategoryModel();
    }

    public function all(): void
    {
        $this->get();
        $this->send($this->categoryModel->getAll());
    }

    public function create(): void
    {
        $this->post();

        $data = $this->getJson();

        if(empty($data->title)) {
            $this->sendError('All fields are required', 400);
        }

        if(strlen($data->title) < 3) {
            $this->sendError('Title must be at least 3 characters', 400);
        }

        $this->categoryModel->setTitleCategory($data->title);
        $question = $this->categoryModel->add();

        if(!$question) {
            $this->sendError('Category not created', 404);
        }

        $this->send($question,201);
    }

    public function show(): void
    {
        $this->get();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        $category = $this->categoryModel->getById($_GET['id']);

        if(!$category) {
            $this->sendError('Category not found', 404);
        }

        $category['quizzies'] = $this->categoryModel->getQuizzies($category['id_category']);

        $this->send($category);
    }

    public function update()
    {
        $this->put();

        $data = $this->getJson();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        if(empty($data->title)) {
            $this->sendError('All fields are required', 400);
        }

        if(strlen($data->title) < 3) {
            $this->sendError('Title must be at least 3 characters', 400);
        }

        $category = $this->categoryModel->getById($_GET['id']);

        if(!$category) {
            $this->sendError('Category not found', 404);
        }

        $this->categoryModel->setIdCategory($category['id_category']);
        $this->categoryModel->setTitleCategory($data->title);
        $updatedCategory = $this->categoryModel->update();

        if(!$updatedCategory) {
            $this->sendError('Category not updated', 404);
        }

        $this->send($updatedCategory);
    }

    public function drop()
    {
        $this->delete();

        if(empty($_GET['id'])) {
            $this->sendError('Id is required', 400);
        }

        $category = $this->categoryModel->getById($_GET['id']);

        if(!$category) {
            $this->sendError('Category not found', 404);
        }

        $this->categoryModel->setIdCategory($category['id']);
        $deletedCategory = $this->categoryModel->delete();

        if(!$deletedCategory) {
            $this->sendError('Category not deleted', 404);
        }

        $this->send([
            'message' => 'Category deleted'
        ]);
    }
}