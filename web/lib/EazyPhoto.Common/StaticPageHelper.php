<?php
    /**
     * Static Page Helper
     */
    class StaticPageHelper {

        /**
         * Form Pages
         * @param      $controlName
         * @param      $pages
         * @param      $value
         * @param bool $showAll
         * @return string
         */
    	public static function FormSelect( $controlName, $pages, $value, $showAll = true ) {
			$xhtml = sprintf( '<select name="%s"><option></option>'
				, $controlName
			);

            $tree = StaticPageUtility::Collapse($pages);
			self::getSubPages( $tree, 0, $value, $showAll, $xhtml );

			$xhtml .= "</select>";
			return $xhtml;
    	}

        /*
    	 * Get Sub Pages
    	 */
		private static function getSubPages($pages, $level, $selectedId, $showAll, &$xhtml) {
			foreach ($pages as $page ) {

				$xhtml .= sprintf( '<option value="%s" %s> %s %s</option>'
					, $page->staticPageId
					, ($selectedId == $page->staticPageId) ? 'selected="selected"' : ""
					, ($level == 0) ? "" : "&nbsp;".  str_repeat( "--", $level ) . "|"
					, $page->title
				);

				if (!empty($page->nodes) && ($showAll || $selectedId != $page->staticPageId)) {
					self::getSubPages($page->nodes, $level + 1, $selectedId, $showAll, $xhtml );
				}
			}
		}
    }