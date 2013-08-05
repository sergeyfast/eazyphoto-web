    </tbody>
</table>

<?
    if ( $grid["canPages"] ) {
        $__paginatorMode = 'pageSizes';
        ?>{increal:tmpl://vt/elements/datagrid/paginator.tmpl.php}<?
    }
?>