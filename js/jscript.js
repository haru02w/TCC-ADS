const validar = cpf => checkAll(prepare(cpf))

const notDig = i => !['.', '-', ' '].includes(i)
const prepare = cpf => cpf.trim().split('').filter(notDig).map(Number)
const is11Len = cpf => cpf.length === 11
const notAllEquals = cpf => !cpf.every(i => cpf[0] === i)
const onlyNum = cpf => cpf.every(i => !isNaN(i))

const calcDig = limit => (a, i, idx) => a + i * ((limit + 1) - idx)
const somaDig = (cpf, limit) => cpf.slice(0, limit).reduce(calcDig(limit), 0)
const resto11 = somaDig => 11 - (somaDig % 11)
const zero1011 = resto11 => [10, 11].includes(resto11) ? 0 : resto11

const getDV = (cpf, limit) => zero1011(resto11(somaDig(cpf, limit)))
const verDig = pos => cpf => getDV(cpf, pos) === cpf[pos]

const checks = [is11Len, notAllEquals, onlyNum, verDig(9), verDig(10)]
const checkAll = cpf => checks.map(f => f(cpf)).every(r => !!r)