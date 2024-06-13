use servicocomunidade;
select * from  servico_orcamento;
select
	*
from
	orcamento as a,
    estado as b,
    estado_orcamento as c,
    servico as d,
    servico_orcamento as e
where
	a.utilizador_id = 2 AND
	a.id = c.orcamento_id AND
    c.estado_id = b.id AND
    a.id = e.orcamento_id AND
    e.servico_id = d.id
	