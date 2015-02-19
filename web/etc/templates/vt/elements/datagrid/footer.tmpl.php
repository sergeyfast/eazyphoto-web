        </tbody>
    </table>
</div>
<?
    /** @var array $grid */
    if ( $grid['canPages'] ) {
        $__paginatorMode = 'pageSizes';
        ?>{increal:tmpl://vt/elements/datagrid/paginator.tmpl.php}<?
    }
?>