{{ header }}

<h2>Order successfully!</h2>

<p>Hi {{ customer_name }},</p>
<p>Thank you for purchasing our products, we will contact you via phone <strong>{{ customer_phone }}</strong> to confirm order!</p>

{{ product_list }}

{% if order_note %}
<p>Note: {{ order_note }}</p>
{% endif %}

<h3>Payment method</h3>
<p>{{ payment_method }}</p>

<br />

<p>If you have any question, please contact us via <a href="mailto:{{ site_admin_email }}">{{ site_admin_email }}</a></p>

{{ footer }}
