/**
 * Mi Plugin Itinerarios - Front
 *
 * Abre/cierra el modal de reserva y rellena el campo oculto del itinerario.
 *
 * @package Mi_Plugin_Itinerarios
 */

(function () {
	'use strict';

	var modal = document.getElementById('mpi-modal-reserva');
	if (!modal) return;

	var overlay = modal.querySelector('.mpi-modal__overlay');
	var closeBtn = modal.querySelector('.mpi-modal__close');
	var reservaBtns = document.querySelectorAll('.mpi-btn-reserva');

	function openModal(itinerarioId, itinerarioTitulo) {
		var field = modal.querySelector('input[name="itinerario-reserva"]');
		if (field) {
			field.value = itinerarioTitulo || itinerarioId || '';
		}
		modal.setAttribute('aria-hidden', 'false');
		modal.classList.add('is-open');
		document.body.style.overflow = 'hidden';
	}

	function closeModal() {
		modal.setAttribute('aria-hidden', 'true');
		modal.classList.remove('is-open');
		document.body.style.overflow = '';
	}

	reservaBtns.forEach(function (btn) {
		btn.addEventListener('click', function () {
			var id = this.getAttribute('data-itinerario-id');
			var titulo = this.getAttribute('data-itinerario-titulo') || '';
			openModal(id, titulo);
		});
	});

	if (overlay) {
		overlay.addEventListener('click', closeModal);
	}
	if (closeBtn) {
		closeBtn.addEventListener('click', closeModal);
	}

	modal.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			closeModal();
		}
	});
})();
