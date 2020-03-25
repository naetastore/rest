<?php 

class Rules_model extends CI_Model
{

	public function product()
	{
		$rules = [
			[
				'field' => 'name',
				'label' => 'Name',
				'rules' => 'required|trim'
			],
			[
				'name' => 'price',
				'label' => 'Price',
				'rules' => 'required|trim|numeric'
			],
			[
				'name' => 'qty',
				'label' => 'Quantity',
				'rules' => 'required|trim|numeric'
			],
			[
				'name' => 'category_id',
				'label' => 'Category Name',
				'rules' => 'required|trim'
			],
			[
				'name' => 'description',
				'label' => 'Description',
				'rules' => 'required|trim|max_length[248]'
			],
			[
				'name' => 'seo_keyword',
				'label' => 'SEO Keyword',
				'rules' => 'required|trim|max_length[128]'
			],
			// [
			// 	'name' => 'image',
			// 	'label' => 'Image',
			// 	'rules' => 'required|trim'
			// ],
			[
				'name' => 'suggested',
				'label' => 'Is Suggested',
				'rules' => 'required|trim|numeric'
			]
		];
		return $rules;
	}

}