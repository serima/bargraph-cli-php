<?php
class BarGraph
{
	private $delimiter;
	private $cols;
	private $max_len_key;
	private $max_len_value;
	private $max_bar_count;
	private $max_value;

	const MARGIN = 130;
	const MIN_BAR_COUNT = 20;

	protected function set_delimiter()
	{
		$this->delimiter = ' ';
	}

	protected function set_cols()
	{
		$this->cols = exec('tput cols');
	}

	protected function set_max_len_key($key)
	{
		$lengths = array_map('strlen', $key);
		$this->max_len_key = max($lengths);
	}

	protected function set_max_len_value($value)
	{
		$lengths = array_map('strlen', $value);
		$this->max_len_value = max($lengths);
	}

	protected function set_max_value($value)
	{
		$this->max_value = max($value);
	}

	protected function set_max_bar_count()
	{
		$try = $this->cols - $this->max_len_key - $this->max_len_value - self::MARGIN;
		$this->max_bar_count = ($try < self::MIN_BAR_COUNT) ? self::MIN_BAR_COUNT : $try;
	}

	protected function get_bar_graph_count($value)
	{
		return round($this->max_bar_count * $value / $this->max_value);
	}

	protected function show_results($key, $value, $bg_count)
	{
		echo sprintf("%s\t%s\t%s\n", $key, $value, $this->get_bar_graph($bg_count));
	}

	protected function get_bar_graph($count)
	{
		return str_repeat("*", $count);
	}

	public function exec()
	{
		$key = [];
		$value = [];
		$max_bar_count = 100;
		$this->set_delimiter();

		while(!feof(STDIN)){
			$line = trim(fgets(STDIN));
			if (empty($line)) {
				continue;
			}
			list($key[], $value[]) = explode($this->delimiter, $line);
		}

		if (count($key) != count($value)) {
			echo "Invalid data\n";
			exit(1);
		}

		$this->set_cols();
		$this->set_max_len_key($key);
		$this->set_max_len_value($value);
		$this->set_max_value($value);
		$this->set_max_bar_count();

		$max_value = max($value);

		for ($i = 0; $i < count($key); $i++) {
			$bg_count = $this->get_bar_graph_count($value[$i]);
			$this->show_results($key[$i], $value[$i], $bg_count);
		}
	}
}

$bg = new BarGraph();
$bg->exec();
