import {
	ExceptionFilter,
	Catch,
	ArgumentsHost,
	HttpException,
	UnauthorizedException,
} from '@nestjs/common';
import { Request, Response } from 'express';

@Catch(UnauthorizedException)
export class ConfiguredSpotViewExceptionFilter implements ExceptionFilter {
	catch(_: HttpException, host: ArgumentsHost): void {
		const ctx = host.switchToHttp();
		const response = ctx.getResponse<Response>();
		const request = ctx.getRequest<Request>();

		const spotId = request.params.id;

		response.redirect(`/spots/${spotId}/redirect`);
	}
}
