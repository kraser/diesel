<?php
/**
 * <pre>Интерфейс службы личного кабинета</pre>
 */
interface OfficeService
{
    public function getData( $params );
    public function setData( $params );
    public function renderTab ( $params );
    public function renderList ( $params );
    public function createRecord ( $params );
}
