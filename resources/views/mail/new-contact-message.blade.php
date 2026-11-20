{{-- Plain-text mail: raw output on purpose, HTML escaping would show &#039; etc. --}}
رسالة جديدة من نموذج التواصل في الموقع.

الاسم: {!! $contact->name !!}
البريد: {!! $contact->email !!}
اللغة: {{ strtoupper((string) $contact->locale) }}

{!! $contact->message !!}

---
اضغط "رد" للرد مباشرة على المرسل.
عرض الرسالة في لوحة التحكم: {{ $dashboardUrl }}
