<?PHP

class Footer_Product_Cat_List_Walker extends Walker {

	public $tree_type = 'product_cat';
	public $db_fields = array ( 'parent' => 'parent', 'id' => 'term_id', 'slug' => 'slug' );
	
	private $totalItems = 999;
	private $itemCount = 0;
	private $totalCats = 0;
	private $catCount = 0;
	private $totalCols = 0;
	
	function __construct($maxItemsPerColumn = 999, $totalCategories, $totalColumns) {
        $this->totalItems = $maxItemsPerColumn;
        $this->totalCats = $totalCategories;
        $this->totalCols = $totalColumns;
    }

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' != $args['style'] )
			return;
		
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}
	
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' != $args['style'] )
			return;

		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";		
		if ( $depth == 0 ) $this->catCount ++;
	}

	public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {
	
		if ( $depth == 0 ) {
			if ($this->itemCount >= $this->totalItems ) $this->itemCount = 0;
			if ($this->itemCount == 0 ){			
				if ($this->catCount > 0 ) $output .= "</ul></li>";
				$output .= "<li class='column'><ul>";
			}
		}
		$this->itemCount ++;
	
		$output .= '<li class="cat-item cat-item-' . $cat->term_id;

		if ( $args['current_category'] == $cat->term_id ) {
			$output .= ' current-cat';
		}

		if ( $args['has_children'] && $args['hierarchical'] ) {
			$output .= ' cat-parent';
		}

		if ( $args['current_category_ancestors'] && $args['current_category'] && in_array( $cat->term_id, $args['current_category_ancestors'] ) ) {
			$output .= ' current-cat-parent';
		}

		$output .=  '"><a href="' . get_term_link( (int) $cat->term_id, 'product_cat' ) . '">' . __( $cat->name, 'woocommerce' ) . '</a>';

		if ( $args['show_count'] ) {
			$output .= ' <span class="count">(' . $cat->count . ')</span>';
		}
		
	}

	public function end_el( &$output, $cat, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		if ( ! $element || 0 === $element->count ) {
			return;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}

?>
