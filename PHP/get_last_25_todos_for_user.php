<?php

function graphql_query(string $endpoint, string $query, array $variables = [], ?string $token = null): array
{
	$headers = ["Content-Type: application/json", "User-Agent: Dunglas's minimal GraphQL client"];
	if (null !== $token) {
		$headers[] = "Authorization: bearer $token";
	}

	if (false === $data = @file_get_contents($endpoint, false, stream_context_create([
		"http" => [
			"method" => "POST",
			"header" => $headers,
			"content" => json_encode(["query" => $query, "variables" => $variables]),
		]
	]))) {
		$error = error_get_last();
		throw new \ErrorException($error["message"], $error["type"]);
	}

	return json_decode($data, true);
}

$query = <<<"GRAPHQL"
query GetUser($user: String!) {
  user (username: $user) {
    todos(limit: 25, order: "completed_at:desc") {
      body
      completed_at
      attachments {
        url
      }
    }
  }
}
GRAPHQL;

$username = "levelsio"
$data = graphql_query("https://wip.co/graphql", $query, ["user" => $username]);
$todos = $data["data"]["user"]["todos"];
?>

<h1>Last 25 Todos</h1>

<?php foreach($todos as $todo) { ?>
<div>
	<p><strong><?php echo $todo["body"]; ?></strong></p>
	<p><?php echo $todo["completed_at"]; ?></p>

	<?php foreach($todo["attachments"] as $attachment) { ?>
		<img src="<?php echo $attachment["url"] ?>" style="max-width: 500px; max-height: 500px;" />
	<?php } ?>
</div>
<?php } ?>
